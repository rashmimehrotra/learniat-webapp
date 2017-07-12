<?php
class Uploaded_images extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Insert uploaded images
     * @param integer $imageTypeId
     * @param string $imagePath
     * @param integer$uploadedBy
     * @return integer
     */
	public function insertUploadedImage($imageTypeId, $imagePath, $uploadedBy)
	{
		$fp=fopen('query.txt','w');
		$data = array(
			'image_type_id' => $imageTypeId,
			'image_path' => $imagePath,
			'uploaded_by' => $uploadedBy,
			'active' => 1,
			'DateUploaded' => 'NOW()'
		);
		
		fwrite($fp,$imageTypeId.' '.$imagePath.' '.$uploadedBy);
		$sql='INSERT INTO `uploaded_images`(`image_type_id`, `image_path`, `uploaded_by`, `active`, `DateUploaded`) VALUES ('.$imageTypeId.', "'.$imagePath.'", '.$uploadedBy.',1,NOW())';
		fwrite($fp,$sql);
		$this->db->query($sql);
		//$this->db->insert('uploaded_images', $data);
		
		
		$id=$this->db->insert_id();
		fwrite($fp,' id= '.$id);
		$query= $this->db->last_query();
		fwrite($fp,' query= '.$query);
		return $id;
	}

    /**
     * Update scribble
     * @param integer $scribbleId
     * @param integer $imageTypeId
     * @param string $imagePath
     * @param integer $uploadedBy
     */
	public function update($scribbleId, $imageTypeId, $imagePath, $uploadedBy)
	{
		$data = array(
			'image_type_id' => $imageTypeId,
			'image_path' => $imagePath,
			'uploaded_by' => $uploadedBy,
			'DateUploaded' => 'NOW()'
		);
		
		$where = "image_id = '$scribbleId'";
		$this->db->update('uploaded_images', $data, $where);
	}

    /**
     * Delete image
     * @param integer $imageId
     */
    public function delete($imageId)
    {
        $data = $this->getUploadedImageDetails($imageId);

        if (!empty($data)) {
            $fileName = LEARNIAT_IMAGE_RELATIVE_PATH . $data->image_path;

            if (file_exists($fileName)) {
                chmod($fileName, 0777);
                unlink($fileName);
            }
        }
        $this->db->delete('uploaded_images', array('image_id' => $imageId));
    }

    /**
     * Get uploaded images details
     * @param integer $imageId
     * @return mixed $data
     */
    public function getUploadedImageDetails($imageId)
    {
        $sql = "SELECT *
		FROM uploaded_images
		WHERE image_id=$imageId ";

        $data = $this->db->query($sql)->row();

        return $data;
    }
}
