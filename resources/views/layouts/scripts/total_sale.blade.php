<script type="text/javascript">
    $(function () {
        var totalSale = $.parseJSON($('#totalSale').val());
        var createDate = $.parseJSON($('#createDate').val());
        var franchise = $('#franchiseId option:selected').text();
        if (!franchise) {
            franchise = $('.franchise').html();
        }
        var maxValue = Math.max.apply(Math, totalSale);
        var tickInterval = 10;
        if (maxValue < 10) {
            tickInterval = 2;
        } else if( maxValue < 100 ) {
            tickInterval = 5;
        }

        $('#container').highcharts({
            credits: {
                enabled: false
            },
            chart: {
                type: 'column'
            },
            title: {
                text: 'Sale Report | '+ franchise
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: createDate,
                title: {
                    text: 'Date'
                }
            },
            yAxis: {
                min: 0,
                tickInterval: tickInterval,
                title: {
                    text: 'range'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Total Sale',
                data: totalSale,
                dataLabels: {
                    enabled: true
                }
            }]
        });

       /* $('body').on('change', 'select.categoryId',function() {
            var categoryId = $(this).val();
            $.ajax({
                type: "GET",
                url:  "analytics?category_id=" + categoryId,
                success: function (data) {
                    var chart = $('#container').highcharts();
                    chart.series[0].setData(data[0]);
                    chart.series[1].setData(data[3]);
                    chart.series[2].setData(data[2]);
                    chart.xAxis[0].setCategories(data[1]);

                }
            });
            return false
        });*/
    });
</script>