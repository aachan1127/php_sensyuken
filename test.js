const WIDTH = 320;
const HEIGHT = 320;

const $fileName = $('#file-name');
const $title = $('#title');
const $description = $('#description');
const $canvas = $('#canvas')[0];
$canvas.width = WIDTH;
$canvas.height = HEIGHT;

const ctx = $canvas.getContext('2d');

let file = null;
const $image = new Image();
let isImage = false; // 選択されているファイルが画像ファイルかどうか
let drawOption = 0;  // リサイズオプション

const $result = $('#result');
const $gallery = $('#gallery');

$(document).ready(function () {
    const $r0 = $('#r0')[0];
    $r0.checked = 'checked';
    drawOption = 0;
    drawImage();
    loadGallery(); // ギャラリーを読み込む
});

$fileName.on('change', function () {
    file = $fileName[0].files[0];
    let fileReader = new FileReader();
    fileReader.readAsDataURL(file);
    fileReader.onloadend = function () {
        $image.src = fileReader.result;
        $image.onload = function () {
            isImage = true;
            drawImage();
        };
        $image.onerror = function () {
            isImage = false;
            drawImage();
        };
    };
});

function drawImage() {
    ctx.fillStyle = '#000';
    ctx.fillRect(0, 0, $canvas.width, $canvas.height);
    if (!isImage) return;

    if (drawOption == 0) {
        ctx.drawImage($image, 0, 0);
    } else if (drawOption == 1) {
        ctx.drawImage($image, 0, 0, $canvas.width, $canvas.height);
    } else if (drawOption == 2) {
        if ($image.width > $image.height) {
            let newHeight = $image.height * WIDTH / $image.width;
            ctx.drawImage($image, 0, (HEIGHT - newHeight) / 2, WIDTH, newHeight);
        } else {
            let newWidth = $image.width * HEIGHT / $image.height;
            ctx.drawImage($image, (WIDTH - newWidth) / 2, 0, newWidth, HEIGHT);
        }
    }
}

function radioChanged() {
    const $r0 = $('#r0')[0];
    const $r1 = $('#r1')[0];
    const $r2 = $('#r2')[0];

    if ($r0.checked) drawOption = 0;
    if ($r1.checked) drawOption = 1;
    if ($r2.checked) drawOption = 2;

    drawImage();
}

async function upload() {
    $canvas.style.display = 'none'; // アップロード結果表示中はキャンバス非表示

    if ($fileName[0].files.length == 0) {
        $result.html('<p>ファイルが選択されていません</p>');
        $result.show();
        return;
    }
    if (!isImage) {
        $result.html('<p>このファイルは画像ファイルではないのでアップロードできません</p>');
        $result.show();
        return;
    }

    const dataURL = $canvas.toDataURL();
    const imgFile = convertToFile(dataURL, file);

    const fd = new FormData();
    fd.append('file_upload', imgFile);
    fd.append('title', $title.val());
    fd.append('description', $description.val());

    const response = await fetch('./test_upload.php', {
        method: 'POST',
        body: fd,
    });

    const html = await response.text();
    const dom = new DOMParser().parseFromString(html, 'text/html');
    $result.html(dom.body.innerHTML);
    $result.show();
    loadGallery(); // アップロード後にギャラリーを更新
}

function convertToFile(dataURL, file) {
    const bin = atob(dataURL.split(',')[1]);
    const buffer = new Uint8Array(bin.length);
    for (let i = 0; i < bin.length; i++) {
        buffer[i] = bin.charCodeAt(i);
    }
    return new File([buffer], file.name, { type: file.type });
}

$fileName.on('click', function () {
    $canvas.style.display = 'block';
    $result.hide();

    ctx.fillStyle = '#000';
    ctx.fillRect(0, 0, $canvas.width, $canvas.height);
});

async function loadGallery() {
    const response = await fetch('./test_gallery.php');
    const html = await response.text();
    $gallery.html(html);
}
