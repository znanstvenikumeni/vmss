<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Uppy</title>
    <link href="https://transloadit.edgly.net/releases/uppy/v1.3.0/uppy.min.css" rel="stylesheet">
</head>
<body>
<div id="drag-drop-area"></div>

<script src="https://transloadit.edgly.net/releases/uppy/v1.3.0/uppy.min.js"></script>
<script>
    var endpoint;
    const xhr = new XMLHttpRequest()
    xhr.open('GET', 'http://vmss.local/auth/042accd8adbd97f915827e6881e5a046/9f24e9543361a0df3c927ec5a44d1e42/url')
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8')
    xhr.send()
    xhr.addEventListener('loadend', function(){
        var uppy = Uppy.Core()
            .use(Uppy.Dashboard, {
                inline: true,
                target: '#drag-drop-area'
            }).use(Uppy.XHRUpload, {
                endpoint: xhr.response
            })
        uppy.on('complete', (result) => {
            console.log('Upload complete! We’ve uploaded these files:', result.successful)
        })
    })


</script>
</body>
</html>
