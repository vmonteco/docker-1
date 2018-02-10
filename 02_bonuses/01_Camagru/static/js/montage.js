

/*
**  Tool functions.
*/

function previewImage(input)
{
	if (input.files && input.files[0]){
		var r = new FileReader();
		
		r.onload = function (e) {
			var img = new Image();
			img.onload = function() {
				context.drawImage(e.target.result, 0, 0, 320, 240);				 
			}
			img.src = e.target.result;
		}
		
		r.readAsDataURL(input.files[0]);
		
	}
}


var webcam = document.getElementById('webcam-preview');


// Elements for taking the snapshot
var file_preview = document.getElementById('file-preview');
var context = file_preview.getContext('2d');
var webcam_preview = document.getElementById('webcam-preview');
var camera_preview = document.getElementById('camera-preview');
var camera_button = document.getElementById('shot');
var submit_button = document.getElementById('submit');

window.addEventListener('keydown', function (e) {
	if (e.which == 13)
		camera_button.click();
});

var shot = document.getElementById("shot");

if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: true }).then(
		function(stream) {
			camera_preview.style.display = "block";
			if (shot)
			{
				shot.style.display = "block";
			}
			webcam.srcObject = stream;
			webcam.play();
		},
		function(stream) {	
			camera_preview.parentElement.removeChild(camera_preview);
			camera_button.parentElement.removeChild(camera_button);
		});
}

/*
** Sound :
*/

var audio = new Audio('/static/sounds/camera.mp3');


var activate_photo_taking_event_listener = function(e){
	addEventListener("change", function(ebis){
		camera_button.disabled = false;
		// Trigger photo take
		var shot = document.getElementById("shot");
		if (shot)
		{
			shot.addEventListener("click", function() {
				audio.play();
				context.drawImage(webcam_preview, 0, 0, 640, 480);
				var file_hidden_input = document.getElementById("file-hidden");
				var file_input = document.getElementById("file");
				file_input.value = "";
				file_hidden_input.value = file_preview.toDataURL().split(';')[1].split(',')[1];
				submit_button.disabled = false;
				//file_hidden_input.value = file_preview.toDataURL();
			});
			document.getElementById("overlay-icon").src = "/static/img/ic_photo_camera_white_48dp.png";
			document.getElementById("overlay-text").style.backgroundColor = "rgba(100, 100, 100, 0.4)";
			document.getElementById("overlay-text").innerHTML = "Tap/click to shot";
		}
		document.getElementById("file").disabled = false;
		//Deactivate current event listener.
		[].forEach.call(
			document.getElementsByClassName("filter-radio"),
			function(eter){
				eter.removeEventListener("change", activate_photo_taking_event_listener);
			}
		);
	})
};

// //In case of change, you can take a photo or add a file
[].forEach.call(
	document.getElementsByClassName("filter-radio"),
	activate_photo_taking_event_listener
);


[].forEach.call(document.getElementsByClassName("non-null-filter-radio"), function(e){
	var self = e;
	addEventListener("change", function(ebis){
		if (self.checked)
		{
			[].forEach.call(document.getElementsByClassName("filter-preview"), function(p){
				p.src = e.nextElementSibling.src;
				p.style.display = '';
			});
		}
	});
});

[].forEach.call(document.getElementsByClassName("null-filter-radio"), function(e){
	var self = e;
	addEventListener("change", function(ebis){
		if (self.checked)
		{
			[].forEach.call(document.getElementsByClassName("filter-preview"), function(p){
				p.src = '';
				p.style.display = 'none';
			});
		}
	});
});


document.getElementById("file").addEventListener("change", function(e) {
	var file_hidden_input = document.getElementById("file-hidden");
	var file_input = document.getElementById("file");
	if (file_input.files && file_input.files[0]){
		submit.disabled = false;
		var r = new FileReader();
		r.onload = function(e){
			var img = new Image();
			img.addEventListener("load", function (){
				context.drawImage(img, 0, 0, 640, 480);
			});
			img.src = e.target.result;
		};
		r.readAsDataURL(file_input.files[0]);
	}
    file_hidden_input.value = "";
});
