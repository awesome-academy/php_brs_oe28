dataset = $('#data').attr('value');
regex = /"\d{1,2}":/g;
dataset = dataset.replace(regex, "");
dataset = dataset.substring(1, dataset.length - 1);
data = dataset.split(",");
label = $('#label').attr('value');
title = $('#title').attr('value');
Chart.defaults.global.defaultFontFamily = 'Quicksand';
Chart.defaults.global.defaultFontColor = '#000000';
Chart.defaults.global.defaultFontSize = 16;

new Chart(document.getElementById("line-chart"), {
    type: 'line',
    data: {
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        datasets: [{
            data: data,
            label: label,
            borderColor: "#3e95cd",
            fill: false
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: title,
        },
        scales: {
            yAxes: [{
                ticks: {
                    stepSize: 1
                }
            }]
        }
    }
});
