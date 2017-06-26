window.addEvent('domready', function(){
	/* thumbnails example , links only */
	new SlideItMoo({itemsVisible:14.5, // the number of thumbnails that are visible
					currentElement: 0, // the current element. starts from 0. If you want to start the display with a specific thumbnail, change this
					thumbsContainer: 'thumbs',
					elementScrolled: 'thumb_container',
					overallContainer: 'gallery_container'});
	
	/* thumbnails example , div containers */
	new SlideItMoo({itemsVisible:3, // the number of thumbnails that are visible
					currentElement: 0, // the current element. starts from 0. If you want to start the display with a specific thumbnail, change this
					thumbsContainer: 'thumbs2',
					elementScrolled: 'thumb_container2',
					overallContainer: 'gallery_container2'});
					
	/* banner rotator example */
	new SlideItMoo({itemsVisible:1, // the number of thumbnails that are visible
					showControls:0, // show the next-previous buttons
					autoSlide:2500, // insert interval in milliseconds
					currentElement: 0, // the current element. starts from 0. If you want to start the display with a specific thumbnail, change this
					transition: Fx.Transitions.Bounce.easeOut,
					thumbsContainer: 'banners',
					elementScrolled: 'banner_container',
					overallContainer: 'banners_container'});
});