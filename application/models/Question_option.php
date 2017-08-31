<?php
class Question_option extends CI_Model
{
	const correctAnswer = 1;

    const incorrectAnswer = 0;

    const mtcFirstColumn = 1;

    const mtcSecondColumn = 2;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get question
	 */
	public function getQuestionModel()
	{
		$CI = &get_instance();
		$CI->load->model('question');
		return $CI->question;
	}
	
	/**
	 * Get uploaded images
	 */
	public function getUploadedImagesModel()
	{
		$CI = &get_instance();
		$CI->load->model('uploaded_images');
		return $CI->uploaded_images;
	}
	
	/**
	 * Get question details
	 *
	 * @param integer $questionId
	 * @param integer $mtcColumn
	 * @return array $questionOptionData
	 */
	public function getMTCQuestionOptionData($questionId, $mtcColumn = NULL)
	{
		$sql = "SELECT question_option_id,question_option, mtc_column, mtc_sequence 
			FROM question_options
			WHERE question_id=$questionId ";
		
		if (!empty($mtcColumn)) {
			$sql .= " AND mtc_column = $mtcColumn  ";
		}
		
		$sql .= "GROUP BY mtc_column, mtc_sequence";
		$questionOptionData = $this->db->query($sql)->result();
		
		return $questionOptionData;
	}
	
	/**
	 * Get mcq and mrq question option data
	 * @param integer $questionId
	 * @return array $questionOptionData
	 */
	public function getMCQAndMRQQuestionOptionData($questionId)
	{
		$sql = "SELECT  question_option_id,question_option, is_answer
		FROM question_options
		WHERE question_id=$questionId ";
		
		$questionOptionData = $this->db->query($sql)->result();
		
		return $questionOptionData;
	}
	
	/**
	 * Get option data by question type
	 * @param string $questionType
	 * @param integer $questionId
	 * @return array $questionOptionData
	 */
	public function getQuestionOptionDataByType($questionType, $questionId)
	{
		$questionOptionData = array();
		
		switch ($questionType) {
			case 'Multiple Choice':
			case 'Multiple Response':
				$questionOptionData = $this->getMCQAndMRQQuestionOptionData($questionId);
				break;
			case 'Match Columns':
				$questionOptionData['firstColumn'] = $this->getMTCQuestionOptionData($questionId, 1);
				$questionOptionData['secondColumn'] = $this->getMTCQuestionOptionData($questionId, 2);
				break;
			case 'Overlay Scribble' :
				$questionOptionData['firstColumn'] = $this->getMTCQuestionOptionData($questionId, 1);
				$questionOptionData['secondColumn'] = $this->getMTCQuestionOptionData($questionId, 2);
				break;
		}
		
		return $questionOptionData;
	}
	
	/**
	 * Get option data by question type id and  question id
	 * @param integer $questionTypeId
	 * @param integer $questionId
	 * @return array $questionOptionData
	 */
	public function getQuestionOptionDataByTypeId($questionTypeId, $questionId)
	{
		$questionOptionData = array();
	
		switch ($questionTypeId) {
			case 1:
			case 2:
				$questionOptionData = $this->getMCQAndMRQQuestionOptionData($questionId);
				break;
			case 3:
				$questionOptionData['firstColumn'] = $this->getMTCQuestionOptionData($questionId, 1);
				$questionOptionData['secondColumn'] = $this->getMTCQuestionOptionData($questionId, 2);
				break;
			case 4 :
				$questionModel = $this->getQuestionModel();
				$questionOptionData = $questionModel->getQuestionScribble($questionId);
				break;
		}
		
		return $questionOptionData;
	}
	
	/**
	 * Insert question options
	 * @param integer $questionTypeId
	 * @param integer $questionId
	 * @param array $options
	 * @param integer $teacherId
	 */
	public function insertQuestionOption($questionTypeId, $questionId, $options, $teacherId)
	{
		$fp=fopen('option.txt','w');
		fwrite($fp,$questionTypeId);
		switch ($questionTypeId) {
			case 1:
			case 2:
				if ((isset($options['corrected']))&& !empty($options['corrected'])) {
					$this->insertInBatch($options['corrected'], $questionId, self::correctAnswer);
				}
				
				if ((isset($options['inCorrected']))&& !empty($options['inCorrected'])) {
					$this->insertInBatch($options['inCorrected'], $questionId, self::incorrectAnswer);
				}
				break;
				
			case 3:
				if ((isset($options['matchColumnA']))&& !empty($options['matchColumnA'])) {
					$this->insertInBatch($options['matchColumnA'], $questionId, NULL, self::mtcFirstColumn, TRUE);
				}
				
				if ((isset($options['matchColumnB']))&& !empty($options['matchColumnB'])) {
					$this->insertInBatch($options['matchColumnB'], $questionId, NULL, self::mtcSecondColumn, TRUE);
				}
				break;
			case 4 :

				$fileName = $this->cropImage($options, $questionId);
				fwrite($fp,$fileName);
                if (!empty($fileName)) {
                    //insert into uploaded_images table
					fwrite($fp,'not empty');
                    $imageModel = $this->getUploadedImagesModel();
                    $scribbleId = $imageModel->insertUploadedImage($imageTypeId = 4, $fileName, $teacherId);
                    fwrite($fp,$scribbleId);
					if (!empty($scribbleId)) {
                        $questionModel = $this->getQuestionModel();
                        $questionModel->updateQuestionScribble($questionId, $scribbleId);
                    }
                }
				break;
		}
	}
	
	/**
	 * Insert in batch
	 * @param array $options
	 * @param integer $questionId
	 * @param integer $isAnswer
	 * @param integer $mtcColumn
	 * @param boolean $mtcSequence
	 */
	public function insertInBatch($options, $questionId, $isAnswer = NULL, $mtcColumn = NULL, $mtcSequence = FALSE)
	{
		$insertData = array();
		foreach ($options AS $key => $option) {
			$insertData[$key] = array(
				'question_id' => $questionId,
				'question_option' => $option
			);
			
			if ($isAnswer !== NULL) {
				$insertData[$key]['is_answer'] = $isAnswer;
			}
			
			if ($mtcColumn !== NULL) {
				$insertData[$key]['mtc_column'] = $mtcColumn;
			}
			if ($mtcSequence === TRUE) {
				$insertData[$key]['mtc_sequence'] = $key + 1;
			}
		}
		
		if (!empty($insertData)) {
			$this->db->insert_batch('question_options', $insertData); 
		}
	}

    /**
     * Upload image
     * @param integer $questionId
     * @return mixed
     * @throws Exception
     */
	function doUpload($questionId)
	{
		$config['upload_path'] = LEARNIAT_IMAGE_RELATIVE_PATH . WEB_APP_IMAGE_PATH;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']	= '1000';
		$config['file_name'] = 'Q-' . $questionId;
		$config['max_width'] = '780';
		$config['max_height'] = '520';
		$this->load->library('upload', $config);
	
		if ( ! $this->upload->do_upload()) {
			throw new Exception($this->upload->display_errors());
		} else {
			return $this->upload->data();
		}
	}

    /**
     * Crop image
     * @param array $options
     * @param integer $questionId
     * @return string $imagePath
     * @throws Exception
     */
    public function cropImage($options, $questionId)
    {
        if (!empty($options['image-data'])) {

            $srcFile = $options['image-data'];
			$fp=fopen('img-src.txt','w');
			fwrite($fp,print_r($options,true));
			//fwrite($fp,$srcFile);
            list($widthImg, $heightImg, $type) = getimagesize($srcFile);
			fwrite($fp,print_r(getimagesize($srcFile),true));
            $optionWidth = (isset($options['w'])) ? $options['w'] : CROP_MIN_WIDTH;
            $optionHeight = (isset($options['h'])) ? $options['h'] : CROP_MIN_HEIGHT;
            $optionX = (isset($options['x'])) ? $options['x'] : 0;
            $optionY = (isset($options['y'])) ? $options['y'] : 0;
			fwrite($fp,'  '.$optionX.'  ',$optionY.'  '.$optionWidth.' '.$optionHeight);
            switch ($type) {
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($srcFile);
                    break;
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($srcFile);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($srcFile);
                    break;
                default:
                    throw new Exception('Unrecognized image type ' . $type);
            }
            // create a new blank image
            $newImage = imagecreatetruecolor($optionWidth, $optionHeight);

            //$black = imagecolorallocate($newImage, 0, 0, 0);
            // Make the background transparent

            if ($optionWidth < CROP_MIN_WIDTH || $optionHeight < CROP_MIN_HEIGHT) {
                $optionWidthRequired = CROP_MIN_WIDTH;
                $optionHeightRequired = CROP_MIN_HEIGHT;
                $newImage = imagecreatetruecolor($optionWidthRequired, $optionHeightRequired);
                $black = imagecolorallocate($newImage, 0, 0, 0);
                //imagecolortransparent($newImage, $black);
                $optionXRequired = (($optionWidthRequired - $optionWidth) / 2);
                $optionYRequired = (($optionHeightRequired - $optionHeight) / 2);
                $this->setTransparency($newImage, $image);
                imagecopyresampled($newImage, $image, $optionXRequired, $optionYRequired, $optionX, $optionY, $optionWidth, $optionHeight, $optionWidth, $optionHeight);


            } else {
                $this->setTransparency($newImage, $image);
                // Copy the old image to the new image
                imagecopyresampled($newImage, $image, 0, 0, $optionX, $optionY, $optionWidth, $optionHeight, $optionWidth, $optionHeight);
            }

            // Output to a temp file
            $fileName = 'q-' . $questionId . '.png';
            $file = LEARNIAT_IMAGE_RELATIVE_PATH . WEB_APP_IMAGE_PATH . $fileName;
            imagepng($newImage, $file);
            $imagePath = WEB_APP_IMAGE_PATH . $fileName;

            return $imagePath;
        }
    }

    /**
     * Set transparency
     * @param object $newImage
     * @param object $imageSource
     */
    public function setTransparency($newImage, $imageSource)
    {
        $transparencyIndex = imagecolortransparent($imageSource);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            $transparencyColor = imagecolorsforindex($imageSource, $transparencyIndex);
        }

        $transparencyIndex = imagecolorallocate($newImage, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($newImage, 0, 0, $transparencyIndex);
        imagecolortransparent($newImage, $transparencyIndex);
    }

    /*
	public function cropImageOld($img, $questionId)
	{
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$fileName = 'q-' . $questionId . '.png';
		$file = LEARNIAT_IMAGE_RELATIVE_PATH . WEB_APP_IMAGE_PATH . $fileName;
		if (file_exists($file)) {
            chmod($file, 0777);
			unlink($file);
		}
		$success = file_put_contents($file, $data);
		$imagePath = WEB_APP_IMAGE_PATH . $fileName;
        chmod($file, 0777);

		return $success ? $imagePath : FALSE;
	}*/
	
	
	/**
	 * Update question options
	 * @param integer $questionTypeId
	 * @param integer $questionId
	 * @param array $options
	 * @param integer $teacherId
	 */
	public function updateQuestionOption($questionTypeId, $questionId, $options, $teacherId)
	{
		switch ($questionTypeId) {
			case 1:
			case 2:
				$this->deleteRecordByQuestionId($questionId);
				if ((isset($options['corrected']))&& !empty($options['corrected'])) {
					$this->insertInBatch($options['corrected'], $questionId, self::correctAnswer);
				}
				
				if ((isset($options['inCorrected']))&& !empty($options['inCorrected'])) {
					$this->insertInBatch($options['inCorrected'], $questionId, self::incorrectAnswer);
				}
				break;
	
			case 3:
				$this->deleteRecordByQuestionId($questionId);
				if ((isset($options['matchColumnA']))&& !empty($options['matchColumnA'])) {
					$this->insertInBatch($options['matchColumnA'], $questionId, NULL, self::mtcFirstColumn, TRUE);
				}
				
				if ((isset($options['matchColumnB']))&& !empty($options['matchColumnB'])) {
					$this->insertInBatch($options['matchColumnB'], $questionId, NULL, self::mtcSecondColumn, TRUE);
				}
				break;
			case 4 :
				$fileName = $this->cropImage($options, $questionId);
				$questionModel = $this->getQuestionModel();
				$questionOptionData = $questionModel->getQuestionScribble($questionId);
				
				//update into uploaded_images table
				if (!empty($questionOptionData)) {
					$imageModel = $this->getUploadedImagesModel();
					$scribbleId = $imageModel->update($questionOptionData->scribble_id, $imageTypeId =4, $fileName, $teacherId);
				}				
				break;
		}
	}
	
	/**
	 * Delete record by questions id
	 * @param integer $questionId
	 */
	public function deleteRecordByQuestionId($questionId)
	{
		$this->db->delete('question_options', array('question_id' => $questionId));
	}

    /**
     * Copy question option for duplicate
     * @param integer $questionTypeId
     * @param integer $questionId
     * @param integer $oldQuestionId
     * @param integer $teacherId
     * @param array $options
     */
    public function copyQuestionOptionForDuplicate($questionTypeId, $questionId, $oldQuestionId, $teacherId, $options = array())
    {

        switch ($questionTypeId) {
            case 1:
            case 2:
            case 3:
                $this->updateQuestionOption($questionTypeId, $questionId, $options, $teacherId);
                break;
            case 4:
                $fileName = $this->copyImage($questionId, $oldQuestionId);
                if (!empty($fileName)) {
                    //insert into uploaded_images table
                    $imageModel = $this->getUploadedImagesModel();
                    $scribbleId = $imageModel->insertUploadedImage($imageTypeId = 4, $fileName, $teacherId);
                    if (!empty($scribbleId)) {
                        $questionModel = $this->getQuestionModel();
                        $questionModel->updateQuestionScribble($questionId, $scribbleId);
                    }
                }
                break;
        }
    }

    /**
     * Copy image
     * @param integer $questionId
     * @param integer $oldQuestionId
     * @throws Exception
     * @return string $imagePath
     */
    public function copyImage($questionId, $oldQuestionId)
    {
        $questionModel = $this->getQuestionModel();
        $questionOptionData = $questionModel->getQuestionScribble($oldQuestionId);
        $webAppImageRootDir = LEARNIAT_IMAGE_RELATIVE_PATH . WEB_APP_IMAGE_PATH;
        $fileOldName = LEARNIAT_IMAGE_RELATIVE_PATH . $questionOptionData->image_path;

        $fileName = 'q-' . $questionId . '.png';
        $fileNewName = $webAppImageRootDir . $fileName;

        if (!copy($fileOldName, $fileNewName)) {
            throw new Exception('failed to copy $fileName...\n');
        }

        $imagePath = WEB_APP_IMAGE_PATH . $fileName;

        return $imagePath;
    }

    /**
     * Delete question option data
     * @param integer $questionOptionId
     * @return boolean $result
     */
    public function deleteQuestionOptionById($questionOptionId)
    {
        return $this->db->delete('question_options', array('question_option_id' => $questionOptionId));
    }
}