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
xhr.open('GET', 'http://vmss.local/auth/JhoZJBB8zq7DoW/d282cea800257c1a7388303c84321f9e2e7a50e0a1778b79071f9996cc2da5d647ec64308f25608b38013cc7a0673e0c322a0df241ff1894fdbc313178cf884a8338b98137aca0a96eb5049b2c24d4e7797e936d267a5a13cc3e837a3cdb727d3521c8b6e1c280e0e493136d1393eceb41c10574d18a64595131b79226799357be4507beda193d683aa5bc1afcf21d6bc271b183a26581f93128a212f530f38e10c79539411cc0c078139a73c8bcd37fe5f89ba7bbba585d03c6cb24c9fec214b32a2f9b80b6e230c45528fb82be0f8e2f01fab932d7065e984569c99961e1571fab9168322a199bfa1af19234999dfbb0dbe5fbd1396fb591a8b2af3d8dc290/url')
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
