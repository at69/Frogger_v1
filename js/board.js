/*
	Board-related functions
*/

// The following gets the inner width of the user navigator - function unused but i keep it here for the record

function getUserWidth() {

	var userWidth;
	
	//for mozilla, netscape, opera, IE7, etc.
	if (typeof window.innerWidth != "undefined") {
		userWidth = window.innerWidth;
	}
	
	//for IE6
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" && document.documentElement.clientWidth != 0) {
		userWidth = document.documentElement.clientWidth;
	}
	
	//older IE versions
	else {
		userWidth = document.getElementsByTagName('body')[0].clientwidth;
	}
	
	return userWidth;
}

// The following gets the inner height of the user navigator - function unused but i keep it here for the record

function getUserHeight() {

	var userHeight;
	
	//for mozilla, netscape, opera, IE7, etc.
	if (typeof window.innerHeight != "undefined") {
		userHeight = window.innerHeight;
	}
	
	//for IE6
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientHeight != "undefined" && document.documentElement.clientHeight != 0) {
		userHeight = document.documentElement.clientHeight;
	}
	
	//older IE versions
	else {
		userHeight = document.getElementsByTagName('body')[0].clientheight;
	}
	
	return userHeight;
	
}

var Board = function () {

	this.lines = new Array(13);		// 13 lines including 2 peace zones, 1 success zone, 5 car zones and 5 wood zones
	
	for(var i = 0; i < 13; i++)
	{
		if(i != 0 && i != 6 && i != 12)		// 0, 6 and 12 correspond to peace and success zones. There is no need for a speed for these.
		{
			this.lines[i] = new Line();
		}
	}
}

var Line = function () {

	var _nextElement = Math.floor(Math.random() * 50) + 1;

	this.speed = Math.floor(Math.random() * 5) + 1; 			// set the speed of elements (wood or cars) on a given line
	
	Object.defineProperties(this, {
	
		nextElement: {
						get: function() { return _nextElement; },
						set: function(newValue) {
							_nextElement = newValue; 
						}
					}
	});
}

//Load frogger images
var frogDown = new Image();
var frogLeft = new Image();
var frogRight = new Image();
var frogUp = new Image();
frogDown.src = "img/frogDown.png";
frogLeft.src = "img/frogLeft.png";
frogRight.src = "img/frogRight.png";
frogUp.src = "img/frogUp.png";

var positionX = 307;
var positionY = 390;
var frogger = new Frog(307, 390, frogUp, 12);

function checkEvent(event) {
	switch(event.keyCode) {
		case 37:	//Key left
			frogger.picture = frogLeft;
			if (!(frogger.positionX >= 0 && frogger.positionX <= 37))
			frogger.positionX -= 27
			else frogger.positionX = 0;
			break;
		case 38:	//Key up
			frogger.picture = frogUp;
			if(frogger.positionY >= 378) frogger.positionY = 348
			else if(frogger.positionY >= 225 && frogger.positionY <= 257) frogger.positionY = 185
			else if(frogger.positionY >= 177 && frogger.positionY <= 225) frogger.positionY = 151
			else if(frogger.positionY <= 58) frogger.positionY -= 33
			else if(frogger.positionY <= 126) frogger.positionY -= 24
			else if(frogger.positionY <= 177) frogger.positionY -= 25
			else frogger.positionY -= 30;
			if(frogger.lineId != 0) frogger.lineId -= 1;
			break;
		case 40:	//Key down
			frogger.picture = frogDown;
			if(frogger.positionY >= 378) frogger.positionY = 390 //go back to the initial position, useless to mode down on the 1st peace line
			else if(frogger.positionY >= 346 && frogger.positionY <= 378) frogger.positionY = 390
			else if(frogger.positionY >= 177 && frogger.positionY <= 225) frogger.positionY = 228
			else if(frogger.positionY >= 150 && frogger.positionY <= 177) frogger.positionY = 185
			else if(frogger.positionY <= 177) frogger.positionY += 24
			else frogger.positionY += 30;
			if(frogger.lineId != 12) frogger.lineId += 1;
			break;
		case 39:	//Key right
			frogger.picture = frogRight;	
			if(!(frogger.positionX >= 600 && frogger.positionX <= 650))
			frogger.positionX += 27
			else frogger.positionX = 613;
			break;
		/*case 13:	//Key enter
		
			break;*/
		default :
			break;
	}
}

