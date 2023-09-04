(function ($) {
    'use strict';

    function EngCvVieMonth(month) {
        let m = month.toLowerCase();
        switch (m) {
            case 'jan': return 'Tháng giêng';
            case 'feb': return 'Tháng hai';
            case 'mar': return 'Tháng ba';
            case 'apr': return 'Tháng tư';
            case 'may': return 'Tháng năm';
            case 'jun': return 'Tháng sáu';
            case 'jul': return 'Tháng bảy';
            case 'aug': return 'Tháng tám';
            case 'sep': return 'Tháng chín';
            case 'oct': return 'Tháng mười';
            case 'nov': return 'Tháng mười một';
            case 'dec': return 'Tháng mười hai';
        }
    }

    donutsChart()

    function lineData() {
        let url = document.documentURI + '/dashboard/line_chart';
        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function (data) {
                lineChart.setData(data)
            }
        });
    }

    lineData()

    var lineChart = new Morris.Line({
        element: 'sales-month',
        xkey: 'period',
        dateFormat: function (x) {
            return  EngCvVieMonth(new Date(x).toDateString().split(' ')[1]);
        },
        xLabelFormat: function (x) {
            return new Date(x).toDateString().split(' ')[1];
        },
        ykeys: ['Doanh số 2020', 'Doanh số 2021'],
        labels: ['Doanh số 2020', 'Doanh số 2021'],
        lineColors: ['#f70a0a', '#196e08'],
        hideHover: 'auto',
        resize: true
    });

    function donutsData() {
        let url = document.documentURI + '/dashboard/donuts_chart';
        return $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function (data) {
                for (let i = 0; i < data.length; i++) {
                    data[i].value = parseFloat(data[i].value)
                }
                return data;
            }
        });
    }

    function donutsChart() {
        return donutsData()
            .then((data) => {
                Morris.Donut({
                    element: 'sales-categories',
                    data: data,
                    colors: ['#41cac0', '#a83b08', '#08a830', '#f376f3', '#8b989b', '#17740b'],
                    formatter: function (y) {
                        return y + "%"
                    },
                    resize: true
                });
            })
    }
})(jQuery);
