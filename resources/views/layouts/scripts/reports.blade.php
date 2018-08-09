<script type="text/javascript">
    $(document).ready(function(){
        $('.print').on('click', function() {
            var innerContents = document.getElementById('printSectionId').innerHTML;
            var popupWinindow = window.open('', '_blank', 'width=600,height=700,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
            popupWinindow.document.open();
            popupWinindow.document.write('<html>' +
                    '<head>' +
                    '<link rel="stylesheet" type="text/css" href="/css/style.css" />' +
                    '</head>' +
                    '<body onload="window.print(); window.close();">' + innerContents +'</body>'+
                    '</html>'
            );
            popupWinindow.document.close();
        });
    });

</script>