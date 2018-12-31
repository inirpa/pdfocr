<html>
	<link rel="stylesheet" href="bootstrap.min.css">
	<script src="jquery.min.js"></script>
	<script src='tesseract.min.js'></script>
	<script src="pdf.js"></script>
	<body>
		<div class="container" style="padding: 30px;">
			<button type="button" id="to-ocr" class="btn btn-primary d-none">To OCR</button>
			<div id="page_status">
				Page status : <span id="current_page">0</span> of <span id="total_page">0</span> completed.
			</div>
			<div id="ocr_status"></div>
			<div class="progress">
		  		<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
			<div id="op-canvas" style="display: block; margin-top: 20px;"></div>
			<input id='pdf' type='file' class="form-control-file" accept="application/pdf" />
		</div>
		<script type="text/javascript">

			//
			// Disable workers to avoid yet another cross-origin issue (workers need the URL of
			// the script to be loaded, and dynamically loading a cross-origin script does
			// not work)
			//
			PDFJS.disableWorker = true;

			//
			// Asynchronous download PDF as an ArrayBuffer
			//
			var pdf = document.getElementById('pdf');
			pdf.onchange = function(ev) {
				$('#to-ocr').removeClass('d-none');
				if (file = document.getElementById('pdf').files[0]) {
					fileReader = new FileReader();
					fileReader.onload = function(ev) {
						PDFJS.getDocument(fileReader.result).then(function getPdfHelloWorld(pdf) {
							totalPages = pdf.numPages;
							$('#total_page').text(totalPages);
							for (var i = 1; i <= totalPages; i++) {							
								pdf.getPage(i).then(function getPageHelloWorld(page) {
								var scale = 1.5;
								var viewport = page.getViewport(scale);


								//
								// Prepare canvas using PDF page dimensions
								//
								var canvas = document.createElement('canvas');
								var context = canvas.getContext('2d');
								canvas.height = viewport.height;
								canvas.width = viewport.width;
								canvas.className = 'c_class';
								// canvas.id = 'canvas_'+i;
								document.getElementById('op-canvas').appendChild(canvas);

								//
								// Render PDF page into canvas context
								//
								var task = page.render({canvasContext: context, viewport: viewport})
								task.promise.then(function(){
								});
							});
						}
						}, function(error){
							console.log(error);
						});
					};
					fileReader.readAsArrayBuffer(file);
				}
			}
		</script>
		<script type="text/javascript">
			$(document).on('click','#to-ocr',function(){
				var cp = 1;
				$('.c_class').each(function(i, obj){				
					Tesseract.recognize(obj.toDataURL()).then(function(result) {
						$.ajax({
							method : 'POST',
							url: "save.php",
							data : {'ocr_text' : result.text},
							success: function(result){
								$('#current_page').text(cp++);
							}
						});
					}).progress(function(result) {
						document.getElementById("ocr_status").innerText = result["status"] + " (" +Math.floor(result["progress"] * 100) + "%)";
						$('.progress-bar').css('width',Math.floor(result["progress"] * 100)+'%');
					});
				})
			});
		</script>
	</body>
</html>