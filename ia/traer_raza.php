<?php
    $url = $_GET['url'];
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Poppins', sans-serif; text-align: center; padding: 20px; margin: 35px; }
    #loader { display: none; }
</style>

<h1 style="color:red;">Adog me!</h1>
<h3><img src="https://icons-for-free.com/iconfiles/png/512/info-131964752893297302.png" height="30"/><br>
     La raza puede no ser certera, y el analizador irá mejorando<br>con el paso del tiempo.</h3>

<div id="image-container"></div>
<div style="margin-top:20px;" id="label-container"></div>
<div id="loader"><img src="https://i.pinimg.com/originals/df/d2/68/dfd2683c9701642c776e31d3b0d603a9.gif" height="120"><br>
                Determinando raza, ésto puede durar un par de segundos...</div>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
<script type="text/javascript">
    const URL = "./model/";
    let model, labelContainer, maxPredictions;

    async function init() {
        const loader = document.getElementById("loader");
        loader.style.display = "block"; // Show loader

        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";

        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();

        const imageUrl = "<?=$url;?>";
        const img = new Image();

        // Wait for the image to finish loading before calling predict
        img.onload = async function () {
            img.crossOrigin = 'anonymous';
            img.width = 200;
            img.height = 200;
            document.getElementById("image-container").appendChild(img);

            labelContainer = document.getElementById("label-container");
            for (let i = 0; i < maxPredictions; i++) {
                labelContainer.appendChild(document.createElement("div"));
            }

            await predict(img);
            loader.style.display = "none"; // Hide loader after prediction
        };

        img.src = imageUrl;
    }

    async function predict(img) {
        const prediction = await model.predict(img);
        for (let i = 0; i < maxPredictions; i++) {

            labelContainer.childNodes[i].innerHTML = "";

            if (prediction[i].probability > 0.5) {
                const classPrediction = prediction[i].className + ": " + prediction[i].probability.toFixed(2);
                labelContainer.childNodes[i].innerHTML = classPrediction;
            }
        }
    }

    init();
</script>