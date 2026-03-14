
import {
    Chart,
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip
  } from 'chart.js';
  Chart.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip
  );
var ctx = document.getElementById('myChart');
if (ctx) {
    const gold      = '#d4a843';
    const violet    = '#7c6af7';
    const gridColor = 'rgba(255, 255, 255, 0.05)';
    const textMuted = '#6868a0';
    const textLight = '#a8a8cc';

    function gradientGold(chart) {
        const g = chart.ctx.createLinearGradient(0, 0, 0, chart.chartArea ? chart.chartArea.bottom : 300);
        g.addColorStop(0, 'rgba(212, 168, 67, 0.25)');
        g.addColorStop(1, 'rgba(212, 168, 67, 0)');
        return g;
    }
    function gradientViolet(chart) {
        const g = chart.ctx.createLinearGradient(0, 0, 0, chart.chartArea ? chart.chartArea.bottom : 300);
        g.addColorStop(0, 'rgba(124, 106, 247, 0.2)');
        g.addColorStop(1, 'rgba(124, 106, 247, 0)');
        return g;
    }

    const chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [
                {
                    label: 'Inscriptions',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: gold,
                    borderWidth: 2,
                    pointBackgroundColor: gold,
                    pointBorderColor: '#0e0e1c',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: function(context) {
                        return gradientGold(context.chart);
                    }
                },
                {
                    label: 'Réservations',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: violet,
                    borderWidth: 2,
                    pointBackgroundColor: violet,
                    pointBorderColor: '#0e0e1c',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: function(context) {
                        return gradientViolet(context.chart);
                    }
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: {
                        color: textLight,
                        font: { family: 'DM Sans', size: 13 },
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#0e0e1c',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    titleColor: textLight,
                    bodyColor: '#e8e8f2',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { family: 'DM Sans', size: 12, weight: '600' },
                    bodyFont: { family: 'DM Sans', size: 13 }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textMuted, font: { family: 'DM Sans', size: 12 } },
                    border: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: textMuted,
                        font: { family: 'DM Sans', size: 12 },
                        stepSize: 1,
                        precision: 0
                    },
                    border: { color: gridColor }
                }
            }
        }
    });

    fetch('/admin/graph')
        .then(r => r.json())
        .then(response => {
            chartInstance.config.data.datasets[0].data = Object.values(response.user);
            chartInstance.config.data.datasets[1].data = Object.values(response.reservation);
            chartInstance.update();
        });
}