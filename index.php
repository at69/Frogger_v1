<!DOCTYPE html>
<html>
	<head>
		<title>Single-user Frogger</title>
		
		<script type="text/javascript" src="js/element.js"></script>
		<script type="text/javascript" src="js/board.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		
		<style>
			/* Set the background of the board and center it in the page */
			#board {	
						background-image: url("img/board.png");
						position: absolute;
						margin-left: -325px;
						left: 50%;
						margin-top: -225px;
						top: 50%;
					}
		</style>
	</head>
	
	<body>
		<canvas id="board" width="650px" height="450px"></canvas>
		<script type="text/javascript">
			window.onload = function() {
				
				var FROGGER_APP = {};
				
				var context = document.getElementById("board").getContext("2d");
				var elements = { };
				var elementsNumber = 0;
				var board = new Board();
				var time = 70;
				
				//timer system
				setInterval(function(){
					time -= 1;
					if(time <= 60){				
						window.onkeydown = checkEvent;
					}
					if(time == 0) defeat();
				}, 1000);	
				
				/*Set the positions of the five water lilies. It's biger than the water lilies but on purpose considering the width of the frogger. 
				  Plus it avoids weird situations where you could have lost for one pixel not in the zone.
				*/
				var wl1 = new WaterLily(16, 78, false);
				var wl2 = new WaterLily(156, 218, false);
				var wl3 = new WaterLily(296, 358, false);
				var wl4 = new WaterLily(436, 498, false);
				var wl5 = new WaterLily(576, 638, false);
				
				//Load car elements images
				FROGGER_APP.sportCar = new Image();
				FROGGER_APP.redCar = new Image();
				FROGGER_APP.blueCar = new Image();
				FROGGER_APP.brownCar = new Image();
				FROGGER_APP.emergencyTruck = new Image();
				FROGGER_APP.sportCar.src = "img/car1.png";
				FROGGER_APP.redCar.src = "img/car2.png";
				FROGGER_APP.blueCar.src = "img/car3.png";
				FROGGER_APP.brownCar.src = "img/car4.png";
				FROGGER_APP.emergencyTruck.src = "img/emergencyTruck.png";
				
				//Load wood images
				FROGGER_APP.smallWood = new Image();
				FROGGER_APP.mediumWood = new Image();
				FROGGER_APP.largeWood = new Image();
				FROGGER_APP.smallWood.src = "img/wood1.png";
				FROGGER_APP.mediumWood.src = "img/wood2.png";
				FROGGER_APP.largeWood.src = "img/wood3.png";

				setInterval(function(){
					
					for(var i = 0; i < 13; i++)
					{
						if(i != 0 && i != 6 && i != 12) // 0, 6 and 12 correspond to peace and success zones.
						{
							board.lines[i].nextElement--;
							if(board.lines[i].nextElement <= 0)
							{
								var positionX = 0;
								var positionY = 0;
								var elementImage;
														
								switch(i) //each line of elements gets its own element and y position on the board.
								{
									case 1:
										elementImage = FROGGER_APP.largeWood;
										positionY = 59;
										width = 178; //Dimensions of each element can be seen in the apercu_plat.png file.
										break;
									case 2:
										elementImage = FROGGER_APP.smallWood;
										positionY = 82;
										width = 84;
										break;
									case 3:
										elementImage = FROGGER_APP.mediumWood;
										positionY = 106;
										width = 116;
										break;
									case 4:
										elementImage = FROGGER_APP.largeWood;
										positionY = 130;
										width = 178;
										break;
									case 5:
										elementImage = FROGGER_APP.smallWood;
										positionY = 155;
										width = 84;
										break;
									case 7 :
										elementImage = FROGGER_APP.emergencyTruck;
										positionY = 225;
										width = 58;
										break;
									case 8 :
										elementImage = FROGGER_APP.redCar;
										positionY = 260;
										width = 34;
										break;
									case 9 :
										elementImage = FROGGER_APP.sportCar;
										positionY = 288;
										width = 34;
										break;
									case 10 :
										elementImage = FROGGER_APP.brownCar;
										positionY = 319;
										width = 43;
										break;
									case 11 :
										elementImage = FROGGER_APP.blueCar;
										positionY = 349;
										width = 50;
										break;
									default :
										elementImage = undefined;
										break;
								}
								
								if (i % 2 == 0) positionX = 0 - width; // on even lines elements enters the board from the left
								else positionX = 650 + width;		   // on odd lines elements enters the board from the righ
								
								//As large woods are... large, we are going to set an inferior apparition time
								if(i == 1 || i == 4) board.lines[i].nextElement = (Math.floor(Math.random() * (50+width)) + (200+width)) / board.lines[i].speed;
								else board.lines[i].nextElement = (Math.floor(Math.random() * (50+width)) + (100+width)) / board.lines[i].speed;
								
								elements[elementsNumber] = new Element(positionX, positionY, elementImage, i, width);
								elementsNumber++;
								
							}
						}
					}
					
					moveElements(board, elements);
					collision(board, elements);
					
					context.clearRect(-178, 0, 828, 450);  	// clear the canvas
				
					//Display lives
					context.beginPath(); 
					context.font = "normal 15px Arial";
					context.fillStyle = "white";
					context.fillText("Lives: " + frogger.lives, 10, 442);
					context.fillText("Time: " + time, 570, 442);
					context.closePath();
					
					
					for(element in elements)
					{
						if(elements[element].picture != undefined)
						{
							context.beginPath(); 
							context.drawImage(elements[element].picture, elements[element].positionX, elements[element].positionY);
							context.closePath(); 
						}
					}
					
					
					context.beginPath(); // Start drawing the shape
					context.drawImage(frogger.picture, frogger.positionX, frogger.positionY);
					context.closePath(); // End drawing the shape
					
				}, 50);
				
				function moveElements(board, elements) {
					for(element in elements)
					{
						
						if(elements[element].picture != frogUp && elements[element].picture != frogDown && elements[element].picture != frogLeft && elements[element].picture != frogRight) {
							if(elements[element].lineId % 2 == 0) // even lines
							{
								elements[element].positionX += board.lines[elements[element].lineId].speed; //element goes to his right at the line's speed
							}		
							else //odd lines
							{
								elements[element].positionX -= board.lines[elements[element].lineId].speed; //element goes to his left at the line's speed
							}
							
							/*
								Here we are going to set up what happens when the element goes out of the board.
								Sure we want the element to be destroyed but we don't want it to be when this element reach the ends of the board (x = 650 or 0).
								In fact we want the element to be destroyed when the entire element goes out the board, else it would be a little weird 
								and not a good thing when frogger is on a wood that goes out for example. 
								We used the same kind of reasoning sooner for the first drawing of each element (cf. lines 134&135 in this code).
							*/
							
							if (elements[element].lineId % 2 == 0) { // on even lines we want to check when the element goes further at the right
							
								if(elements[element].positionX >= 650 + elements[element].width) 
								{
										delete elements[element]; //we delete the element
								}
							}
							else {  // on odd lines we want to check when the element goes further at the left
							
								if(elements[element].positionX <= 0 - elements[element].width) 
								{
										delete elements[element]; //we delete the element
								}
							}
						}
						else if(frogger.lineId >= 1 && frogger.lineId <=5 && elements[element].lineId != 0) {
						
							if(frogger.lineId % 2 == 0) // even lines
							{
								if(frogger.positionX + 37 > 651) defeat();
								frogger.positionX += board.lines[elements[element].lineId].speed; //frogger goes to his right at the line's speed	
							}		
							else //odd lines
							{
								if(frogger.positionX < -1) defeat();
								frogger.positionX -= board.lines[elements[element].lineId].speed; //frogger goes to his left at the line's speed		
							}
						}
					}
					
				}
				
				function collision(board, elements) {
				
					var froggerX1 = frogger.positionX;
					var froggerX2 = frogger.positionX + 37;
					var onPlatform = false;
					
					if(frogger.lineId != 0 && frogger.lineId != 6 && frogger.lineId != 12) {  
					
						for(element in elements) {
							
							var elementX1 = elements[element].positionX;
							var elementX2 = elements[element].positionX + elements[element].width;
							
							if(elements[element].lineId == frogger.lineId){ //we only check for collisions on the line the frogger is
								
								if(elements[element].lineId >= 7 && elements[element].lineId <= 11) { //on enemies lines
									
									if(froggerX1 <= elementX2 && froggerX2 >= elementX1) defeat();
								}
								else if(elements[element].lineId <= 5 && elements[element].lineId >= 1) { //on platform lines
									
									if(elementX1 <= froggerX1 && elementX2 >= froggerX2) {
										onPlatform = true;
										break;
									} else onPlatform = false;
								}
							}
						}
						
						if(onPlatform == true) {
						
							if(frogger.lineId <= 5 && frogger.lineId >= 1) { //on platform lines
								var array = { };
								var number = 0;
								array[number] = new Element(frogger.positionX, frogger.positionY, frogger.picture, frogger.lineId, 37);
								moveElements(board, array);
								
							}
						}
						else {
							
							if(frogger.lineId <= 5 && frogger.lineId >= 1) { //on platform lines
								defeat();
							}
						}
					}
					else if(frogger.lineId == 0) { //when frogger is on the water lilies line
						
						if(!( 
							wl1.positionX1 <= froggerX1 && froggerX2 <= wl1.positionX2
						 || wl2.positionX1 <= froggerX1 && froggerX2 <= wl2.positionX2
						 || wl3.positionX1 <= froggerX1 && froggerX2 <= wl3.positionX2
						 || wl4.positionX1 <= froggerX1 && froggerX2 <= wl4.positionX2
						 || wl5.positionX1 <= froggerX1 && froggerX2 <= wl5.positionX2
						)) defeat();
						else {
							if ((froggerX1 >= wl1.positionX1) && (froggerX1 <= wl1.positionX2)) {
								if(wl1.taken == true) defeat();
								else {
									wl1.taken = true;
									wlTaken();
								}
							}
							else if ((froggerX1 >= wl2.positionX1) && (froggerX1 <= wl2.positionX2)) {
								if(wl2.taken == true) defeat();
								else {
									wl2.taken = true;
									wlTaken();
								}
							}
							else if ((froggerX1 >= wl3.positionX1) && (froggerX1 <= wl3.positionX2)) {
								if(wl3.taken == true) defeat();
								else {
									wl3.taken = true;
									wlTaken();
								}
							}
							else if ((froggerX1 >= wl4.positionX1) && (froggerX1 <= wl4.positionX2)) {
								if(wl4.taken == true) defeat();
								else {
									wl4.taken = true;
									wlTaken();
								}
							}
							else if ((froggerX1 >= wl5.positionX1) && (froggerX1 <= wl5.positionX2)) {
								if(wl5.taken == true) defeat();
								else {
									wl5.taken = true;
									wlTaken();
								}
							}
							
							if(wl1.taken == true && wl2.taken == true && wl3.taken == true && wl4.taken == true && wl5.taken == true) {
								alert("Victory. Game will restart once you close this pop-up.");
								location.reload();	
							}
						}
					}			
				}
				
				//whenever a water lilly is reached
				function wlTaken(){

					elements[elementsNumber] = new Frog(frogger.positionX, frogger.positionY, frogDown, frogger.lineId);
					elementsNumber++;
					alert("Congrats, you've reach one water lilly, try to reach the other ones.");
					time = 60;
					frogger.positionX = 307;
					frogger.positionY = 390;
					frogger.picture = frogUp;
					frogger.lineId = 12;	
				}
				
				//in case a collision happens and when you lost all your lives
				function defeat(){
				
					if(frogger.lives > 0) {
						
						frogger.lives -= 1;
						alert("You lost a life, you have "+ frogger.lives + " remaining.");
						time = 60;
						frogger.positionX = 307;
						frogger.positionY = 390;
						frogger.picture = frogUp;
						frogger.lineId = 12;
					}
					
					if(frogger.lives == 0) {
					
						alert("Sorry but you lost! Try again ;)");
						location.reload();	
					}
				}
			}

			$( function() {
				$("button[data-href]").click( function() {
					location.href = $(this).attr("data-href");
				});
			});
		</script>
		<p style="text-align: center;">Utilisez les fl&egrave;ches du clavier pour d&eacute;placer Frogger.<br/>
		Vous ne pourrez bouger la grenouille les 10 premi&egrave;res secondes, afin qu'il y ait suffisamment d'&eacute;l&eacute;ments sur le plateau pour que ce ne soit pas trop facile!
		</p>
	</body>
</html>