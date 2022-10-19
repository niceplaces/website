<?php
require_once "../data/requires.php";
require_once '../utils.php';
error_reporting(E_ALL && ~E_NOTICE);
$conn = mySqlConnect();
$dao = new DaoPlacesV3($conn, VERSION, MODE);
$lang = 'it';
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
}
if (isset($_GET['day'])) {
	$placeOfTheDay = $dao->getPlaceOfTheDay(strtoupper(get_lang()), $_GET["day"]);
} else {
	$placeOfTheDay = $dao->getPlaceOfTheDay(strtoupper(get_lang()));
}
if ($lang == "en") {
	if ($placeOfTheDay["name_en"] != "") {
		$placeOfTheDay["name"] = $placeOfTheDay["name_en"];
	}
	$placeOfTheDay["region"] = $placeOfTheDay["region_en"];
	$placeOfTheDay["description"] = $placeOfTheDay["description_en"];
	for ($i = 0; $i < count($lists); $i++) {
		$lists[$i]["name"] = $lists[$i]["name_en"];
	}
	for ($i = 0; $i < count($regions); $i++) {
		if ($regions[$i]["name_en"] != "") {
			$regions[$i]["name"] = $regions[$i]["name_en"];
		}
	}
}
?>
<html>

<head>
</head>

<body>
	<canvas id="canvas" width="1080" height="1080" style="width: 200px; height: 200px"></canvas>
	<?php for ($i = 0; $i < 10; $i++) { ?>
		<canvas id="canvas<?php echo $i ?>" width="1080" height="1080" style="width: 200px; height: 200px; display: none"></canvas>
	<?php } ?>
	<p id="text"></p>
	<img id="place-img" src="<?php echo $BASE_URL . "data/photos/" . MODE . "/" . $placeOfTheDay["image"] ?>" style="display: none">
	<div id="description" style="display: none"><?php echo $placeOfTheDay["description"] ?></div>
	<div id="author-id" style="display: none"><?php echo $placeOfTheDay["author"] ?></div>
	<div id="author" style="display: none"><?php 
	if ($placeOfTheDay["author"] != 1){
		switch ($lang){
			case "it":
				echo "Autore: ";
				break;
			case "en":
				echo "Author: ";
				break;
			default:
				break;
		}
		switch ($placeOfTheDay["author"]){
			case "2":
				echo "Pro Loco Sovicille";
				break;
			case "3":
				echo "Cammino d'Etruria";
				break;
			case "4":
				echo "Pro Loco Murlo";
			default:
				break;
		}
	}
	?></div>
	<div id="name" style="display: none"><?php echo $placeOfTheDay["name"] ?></div>
	<div id="area" style="display: none"><?php echo $placeOfTheDay["area"] ?></div>
	<div id="region" style="display: none"><?php echo $placeOfTheDay["region"] ?></div>
	<div id="img-credits" style="display: none"><?php echo $placeOfTheDay["img_credits"] ?></div>
	<img id="logo-np" src="../assets/logo_website.png" style="display: none">
	<script>
		window.onload = function() {

			let color_nice_places = "#188a8d"
			let color_pro_loco_sovicille = "#ff6f00"
			let color_cammino_detruria = "#F8D4AD"
			let color_pro_loco_murlo = "#048ecc"

			function createGradient(alpha=false){
				let gradient = ctx.createLinearGradient(0, 0, width, width)
				if (author_id === "2"){
					gradient.addColorStop(0, color_pro_loco_sovicille + (alpha ? "cc" : ""))
				} else if (author_id === "3"){
					gradient.addColorStop(0, color_cammino_detruria + (alpha ? "cc" : ""))
				} else if (author_id === "4"){
					gradient.addColorStop(0, color_pro_loco_murlo + (alpha ? "cc" : ""))
				} else {
					gradient.addColorStop(0, color_nice_places + (alpha ? "cc" : ""))
				}
				gradient.addColorStop(1, "#60dd8e")
				return gradient
			}

			let c = document.getElementById("canvas")
			let ctx = c.getContext("2d")
			let logo_np = document.getElementById("logo-np")
			let img = document.getElementById("place-img")
			let name = document.getElementById("name").innerHTML
			let description = document.getElementById("description").innerHTML
			let author_id = document.getElementById("author-id").innerHTML
			let author = document.getElementById("author").innerHTML
			let area = document.getElementById("area").innerHTML
			let region = document.getElementById("region").innerHTML
			let img_credits = document.getElementById("img-credits").innerHTML

			let social_text = document.getElementById("text")
			social_text.innerHTML = name + " (#" + area + ", #" + region + ")<br/><br/>" +
				description.replaceAll('\n', '<br/>') + "<br/><br/>"
			if (author !== ""){
				social_text.innerHTML += author + "<br/><br/>"
			}
			social_text.innerHTML +=
				"#niceplaces #" + area.toLowerCase() + " #" + region.toLowerCase() + " #italia " +
				"#igers" + area.toLowerCase() + " #igers" + region.toLowerCase() + " " +
				"#volgo" + area.toLowerCase() + " #volgo" + region.toLowerCase() + " " +
				"#androidapp #app #beautifulplaces #discover #igers #igersitalia #navigation " +
				"#nofilter #photography #picoftheday #placeoftheday #placestovisit #tourism " +
				"#tourism #touristlife #travel #volgo #volgoitalia #wonderfulplaces"
			let width = 1080
			let margin = 30
			let radius = 50
			createImage(ctx)
			let text = wrapText(ctx, description, 440, 50)
			let lines_per_frame = 18
			for (let i = 0; i <= text.length / lines_per_frame; i++) {
				let begin = i * lines_per_frame
				let end = (i + 1) * lines_per_frame
				let text_slice = text.slice(begin, end)
				if (text_slice.length > 0){
					createDesc(text_slice, i)
				}
			}

			function createImage(ctx) {
				ctx.imageSmoothingQuality = "high"

				let ar = img.width / img.height // Aspect Ratio
				// Simulate {background-position: center; background-size: cover}
				let dx, dy, dWidth, dHeight
				if (img.width > img.height) {
					dx = margin - ((width - margin * 2) * ar) / 2 + width / 2
					dy = margin
					dWidth = (width - margin * 2) * ar
					dHeight = width - margin * 2
				} else {
					dx = margin
					dy = margin - ((width - margin * 2) * (1/ar)) / 2 + width / 2
					dWidth = width - margin * 2
					dHeight = (width - margin * 2) * (1/ar)
				}
				ctx.drawImage(img, dx, dy, dWidth, dHeight)

				let center_x = 150
				let center_y = 130

				// Text block
				ctx.beginPath()
				ctx.moveTo(margin + radius, width - center_y)
				ctx.lineTo(width, width - center_y)
				ctx.lineTo(width, width)
				ctx.lineTo(margin, width)
				ctx.lineTo(margin, width - center_y + radius)
				ctx.arc(margin + radius, width - center_y + radius, radius, Math.PI, Math.PI * 1.5)
				ctx.fillStyle = "#ffffff77"
				ctx.fill()

				ctx.beginPath()
				// Left border
				ctx.rect(0, 0, margin, width)
				// Top border
				ctx.rect(0, 0, width, margin)
				// Right border
				ctx.rect(width - margin, 0, width - margin, width)
				// Bottom border
				ctx.rect(0, width - margin, width, width)
				ctx.fillStyle = createGradient()
				ctx.fill()

				ctx.beginPath()
				// Bottom-left border
				ctx.arc(margin + radius, width - margin - radius, radius, Math.PI / 2, Math.PI)
				ctx.lineTo(0, width - margin - radius)
				ctx.lineTo(0, width)
				ctx.lineTo(margin + radius, width)
				ctx.lineTo(margin + radius, width - margin)
				ctx.fill()

				ctx.beginPath()
				// Top-right border
				ctx.arc(width - margin - radius, margin + radius, radius, 0, Math.PI * 1.5, true)
				ctx.lineTo(width - margin - radius, 0)
				ctx.lineTo(width, 0)
				ctx.lineTo(width, margin + radius)
				ctx.lineTo(width - margin, margin + radius)
				ctx.fill()

				// Top-left badge
				ctx.beginPath()
				ctx.arc(margin + radius, margin + radius, radius, Math.PI * 1.5, Math.PI, true)
				ctx.lineTo(0, 0)
				ctx.fill()

				// Bottom-right badge
				ctx.beginPath()
				center_x = width - center_x + 70
				center_y = width - center_y + 50
				ctx.arc(center_x, center_y, radius, Math.PI * 1.5, Math.PI, true)
				ctx.lineTo(center_x - radius, width - margin - radius)
				ctx.arc(center_x - radius * 2, width - margin - radius, radius, 0, Math.PI / 2)
				ctx.lineTo(center_x - radius * 2, width)
				ctx.lineTo(width, width)
				ctx.lineTo(width, center_y - radius * 2)
				ctx.lineTo(width - margin, center_y - radius * 2)
				ctx.arc(width - margin - radius, center_y - radius * 2, radius, 0, Math.PI / 2)
				ctx.lineTo(center_x, center_y - radius)
				ctx.fill()

				ctx.fillStyle = "black"
				ctx.font = "italic 600 50px Open Sans"
				ctx.fillText(name, margin + 50, width - 80)
				ctx.fillStyle = "#222222"
				ctx.font = "italic 600 35px Open Sans"
				ctx.fillText(area + ", " + region, margin + 50, width - 40)
				ctx.font = "italic 600 20px Open Sans"
				ctx.fillStyle = "#ffffffcc"
				ctx.textAlign = "right";
				ctx.fillText(img_credits, width - margin - 30, width - margin - 110)

				ar = logo_np.width / logo_np.height // Aspect Ratio
				let newwidth = 80
				let newheight = newwidth / ar
				ctx.drawImage(logo_np, width - margin - newwidth, width - margin - newheight, newwidth, newheight)
			}

			function createDesc(text, canvas_id) {
				let c = document.getElementById("canvas" + canvas_id)
				c.style.display = "inline"
				let ctx = c.getContext("2d")
				ctx.imageSmoothingQuality = "high"

				let width = 1080
				let margin = 30
				let radius = 50

				let ar = img.width / img.height // Aspect Ratio
				// Simulate {background-position: center; background-size: cover}
				let dx, dy, dWidth, dHeight
				if (img.width > img.height) {
					dx = margin - ((width - margin * 2) * ar) / 2 + width / 2
					dy = margin
					dWidth = (width - margin * 2) * ar
					dHeight = width - margin * 2
				} else {
					dx = margin
					dy = margin - ((width - margin * 2) * (1/ar)) / 2 + width / 2
					dWidth = width - margin * 2
					dHeight = (width - margin * 2) * (1/ar)
				}
				ctx.drawImage(img, dx, dy, dWidth, dHeight)

				let center_x = 150
				let center_y = 130

				// Text block
				ctx.fillStyle = createGradient(true)
				ctx.fillRect(0, 0, width, width);

				ctx.beginPath()
				// Left border
				ctx.rect(0, 0, margin, width)
				// Top border
				ctx.rect(0, 0, width, margin)
				// Right border
				ctx.rect(width - margin, 0, width - margin, width)
				// Bottom border
				ctx.rect(0, width - margin, width, width)
				gradient = ctx.createLinearGradient(0, 0, width, width);

				ctx.fillStyle = createGradient()
				ctx.fill()

				ctx.beginPath()
				// Bottom-left border
				ctx.arc(margin + radius, width - margin - radius, radius, Math.PI / 2, Math.PI)
				ctx.lineTo(0, width - margin - radius)
				ctx.lineTo(0, width)
				ctx.lineTo(margin + radius, width)
				ctx.lineTo(margin + radius, width - margin)
				ctx.fill()

				ctx.beginPath()
				// Top-right border
				ctx.arc(width - margin - radius, margin + radius, radius, 0, Math.PI * 1.5, true)
				ctx.lineTo(width - margin - radius, 0)
				ctx.lineTo(width, 0)
				ctx.lineTo(width, margin + radius)
				ctx.lineTo(width - margin, margin + radius)
				ctx.fill()

				// Top-left badge
				ctx.beginPath()
				ctx.arc(margin + radius, margin + radius, radius, Math.PI * 1.5, Math.PI, true)
				ctx.lineTo(0, 0)
				ctx.fill()

				// Bottom-right badge
				ctx.beginPath()
				center_x = width - center_x + 70
				center_y = width - center_y + 50
				ctx.arc(center_x, center_y, radius, Math.PI * 1.5, Math.PI, true)
				ctx.lineTo(center_x - radius, width - margin - radius)
				ctx.arc(center_x - radius * 2, width - margin - radius, radius, 0, Math.PI / 2)
				ctx.lineTo(center_x - radius * 2, width)
				ctx.lineTo(width, width)
				ctx.lineTo(width, center_y - radius * 2)
				ctx.lineTo(width - margin, center_y - radius * 2)
				ctx.arc(width - margin - radius, center_y - radius * 2, radius, 0, Math.PI / 2)
				ctx.lineTo(center_x, center_y - radius)
				ctx.fill()

				ctx.fillStyle = "black"
				ctx.font = "200 40px Open Sans"
				drawText(ctx, text, margin + 100, margin + 80, 50)
				ctx.font = "italic 500 25px Open Sans"
				ctx.fillText(author, margin + 50, width - margin - 20)

				ar = logo_np.width / logo_np.height // Aspect Ratio
				let newwidth = 80
				let newheight = newwidth / ar
				ctx.drawImage(logo_np, width - margin - newwidth, width - margin - newheight, newwidth, newheight)
			}

			// Adapted from https://codepen.io/nishiohirokazu/pen/jjNyye
			function wrapText(context, text, maxWidth, lineHeight) {
				let output = []
				let y = 0
				let paragraphs = text.split('\n')
				for (let i = 0; i < paragraphs.length; i++) {
					var words = paragraphs[i].split(' ');
					let line = '';
					for (var n = 0; n < words.length; n++) {
						var testLine = line + words[n] + ' ';
						var metrics = context.measureText(testLine);
						var testWidth = metrics.width;
						if (testWidth > maxWidth && n > 0) {
							output.push(line)
							line = words[n] + ' ';
							y += lineHeight;
						} else {
							line = testLine;
						}
					}
					output.push(line)
					y += lineHeight
				}
				return output
			}

			// Adapted from https://codepen.io/nishiohirokazu/pen/jjNyye
			function drawText(context, text, x, y, lineHeight) {
				for (let i = 0; i < text.length; i++) {
					context.fillText(text[i], x, y);
					y += lineHeight;
				}
			}
		}
	</script>
</body>

</html>