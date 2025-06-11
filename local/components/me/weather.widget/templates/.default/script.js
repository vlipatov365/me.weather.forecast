BX.namespace('BX.Weather.Widget');

BX.Weather.Widget.YearCharts = function (
	weeks,
	temperature,
	humidity,
	pressure
) {
	this.weeks = weeks;
	this.temperature = temperature;
	this.humidity = humidity;
	this.pressure = pressure;
};

BX.Weather.Widget.YearCharts.prototype = {
	weeks: [],
	temperature: [],
	humidity: [],
	pressure: [],
	renderTemperatureChart(canvasId, params = {}) {
		new Chart(BX(canvasId), {
			type: 'line',
			data: {
				labels: this.weeks,
				datasets: [{
					label: BX.message('WEATHER_WIDGET_TEMPERATURE'),
					data: this.temperature,
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: false
					}
				}
			}
		});
	},
	renderHumidityChart(canvasId, params = {}) {
		new Chart(BX(canvasId), {
			type: 'bubble',
			data: {
				labels: this.weeks,
				datasets: [{
					label: BX.message('WEATHER_WIDGET_HUMIDITY'),
					data: this.humidity,
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: false
					}
				}
			}
		});
	},
	renderPressureChart(canvasId, params = {}) {
		new Chart(BX(canvasId), {
			type: 'bubble',
			data: {
				labels: this.weeks,
				datasets: [{
					label: BX.message('WEATHER_WIDGET_PRESSURE'),
					data: this.pressure,
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: false
					}
				}
			}
		});
	},
};

