var randomScalingFactor = function() {
    return Math.round(Math.random() * 100);
};

var membersChartConfig = {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [
                randomScalingFactor(),
                randomScalingFactor(),
                randomScalingFactor(),
            ],
            backgroundColor: [
                '#63cdda',
                '#f8a5c2',
                '#34ace0',
            ],
            label: 'Dataset 1'
        }],
        labels: [
            'Distributor',
            'Unit Manager',
            'Manager'
        ]
    },
    options: {
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: true,
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

var growthChartConfig = {
    type: 'line',
    data: {
        labels: [
            'Distributor',
            'Unit Manager',
            'Manager'
        ],
        datasets: [{
            backgroundColor: '#70a1ff',
            borderColor: '#1e90ff',
            data: [
                randomScalingFactor(),
                randomScalingFactor(),
                randomScalingFactor(),
            ],
            label: 'Dataset',
            fill: 'start'
        }]
    },
    options: {
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: true,
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

window.onload = function() {
    var topCtx = document.getElementById('membersChart').getContext('2d');
    window.membersDoughnut = new Chart(topCtx, membersChartConfig);

    var growthCtx = document.getElementById('growthChart').getContext('2d');
    window.growthChart = new Chart(growthCtx, growthChartConfig);
};
