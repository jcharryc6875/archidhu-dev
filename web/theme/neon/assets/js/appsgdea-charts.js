/**
 *	Neon Charts Scripts
 *
 *	Developed by Arlind Nushi - www.laborator.co
 */

;(function($, window, undefined)
{
	"use strict";
	
	$(document).ready(function()
	{ 
		$.InitAllGraph = function(dataviews, tgraph, element_chart = null)
		{
			if(tgraph == "BarCustom") 
			{
				const ctx = (element_chart == null || element_chart === "") ? document.getElementById('myChart7') : document.getElementById(element_chart);

				let ilist_colors = [];
				let datasets_custom = [];
				for (let i = 0; i < Object.keys(dataviews['labels']).length; i++) 
				{
					let customColor = ColorCode();
					ilist_colors[i] = customColor;
					datasets_custom[i] = {
						label: [ dataviews['labels'][i] ],
						data: [ parseInt(dataviews['totales'][i]) ],
						backgroundColor: customColor,
						borderColor: customColor,
						borderWidth: 4,
						datalabels: {
							formatter: function (value, ctx) {
								//return 'Total: ' + value;
								return value;
							},
							color: "white",
							align: "center",
							font: {
							  weight: 'bold'
							}
						}
					};
				}
				
				const data = {
					labels: [ dataviews['labels'] ],
					datasets: datasets_custom
				};

				new Chart(ctx, {
					type: 'bar',
					data: data,
					options: {
						responsive: false,
						width: '100%',
						height: '400px', 
						plugins: {
							tooltip: {
								callbacks: {
									title: function(context) {
										let title = context[0].dataset.label[0];
										return title;
									}
								}
							}
						},
						scales: {
							x: {
								stacked: false,
								ticks: {
									display: false, //oculta los labels del eje x
								}
							},
							y: {
								stacked: false
							}
						}//, maintainAspectRatio: false // Permite ajustar la altura de la gráfica 
					},
					plugins: [ChartDataLabels]
				});
			
				if ( $("#exportBtn").length ) {
					// Funcion para exportar el grafico
					document.getElementById('exportBtn').addEventListener('click', function() 
					{
						const link = document.createElement('a');
						link.href = document.getElementById('myChart7').toDataURL('image/png', 1.0); // 1.0 es la calidad de la imagen
						link.download = 'grafico.png'; // Nombre del archivo
						link.click();
					});
				}
			}
			else if(tgraph == "DonutCustom") 
			{
					const ctx = (element_chart == null || element_chart === "") ? document.getElementById('myChart8') : document.getElementById(element_chart);
	
					let ilist_colors = [];
					for (let i = 0; i < Object.keys(dataviews['labels']).length; i++) 
					{
						ilist_colors[i] = ColorCode();
					}
	
					const data = {
						labels: dataviews['labels'],
						datasets: [{
							label: 'Total',
							data: dataviews['totales'],
							backgroundColor: ilist_colors,
							hoverOffset: 4
						}]
					};

					const chart = new Chart(ctx, {
						type: 'pie', // o el tipo de gráfico que estés usando
						data: data,
						plugins: [ChartDataLabels], // Registrar el plugin
						options: 
						{
							responsive: true,
							maintainAspectRatio: false,
							layout: {
								padding: {
								  right: 2
								}
							},

							plugins: {
								legend: {
									position: 'right',
									labels: {
									  generateLabels: function(chart) 
									  {
										  const data = chart.data;
										  const dataset = data.datasets[0];
										  const total = dataset.data.reduce((acc, val) => acc + Number(val), 0);
										
										  return data.labels.map((label, i) => {
											const value = Number(dataset.data[i]);
											const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
											let elLabel = label.length > 45 ? label.substr(0, 45) + '...' : label;
										
											  return {
											  text: `${elLabel} (${percentage}%)`,
											  fillStyle: dataset.backgroundColor[i],
											  strokeStyle: dataset.backgroundColor[i],
											  index: i
											};
										  });
									  },
									  padding: 20,
									  boxWidth: 150,
									  usePointStyle: true,
									  pointStyle: 'circle',
									  font: {
										  size: 12,
										  weight: 'bold'
									  },
									  color: '#333'
									}
								},	
								datalabels: {
									display: false
									// Otras opciones de datalabels si las necesitas
									//color: '#000',
									//anchor: 'end',
									//align: 'start',
								}
							}
						}
					  }); 
					  
					 
					
					if ( $("#exportBtn02").length ) {
						// Funcion para exportar el grafico
						document.getElementById('exportBtn02').addEventListener('click', function() 
						{
							const link = document.createElement('a');
							link.href = document.getElementById('myChart8').toDataURL('image/png', 1.0); // 1.0 es la calidad de la imagen
							link.download = 'grafico02.png'; // Nombre del archivo
							link.click(); 
						});
					}
			}
			else if(tgraph == "BarStacked")
			{
				const ctx = (element_chart == null || element_chart === "") ? document.getElementById('myChart19') : document.getElementById(element_chart);

				const labels = dataviews['labels'];
				const data = {
				labels: labels,
				datasets: [
					{
						label: 'Con Respuesta',
						data: dataviews['totales']['total_con_resp'],
						backgroundColor: '#f7cb1f',
						stack: 'Stack 0',
						datalabels: {
							formatter: function (value, ctx) {
									if(value !== 0) { return value; }
									else{ return ""; }
							},
							color: "white",
							align: "center",
							font: {
								weight: 'bold'
							}
						}
					},
					{
						label: 'Sin Respuesta',
						data: dataviews['totales']['total_sin_resp'],
						backgroundColor: '#d42020',
						stack: 'Stack 0',
						datalabels: {
							formatter: function (value, ctx) {
								if(value !== 0) { return value; }
								else{ return ""; }
							},
							color: "white",
							align: "center",
							font: {
								weight: 'bold'
							}
						}
					},
					{
						label: 'Total Radicados',
						data: dataviews['totales']['total_radicados'],
						backgroundColor: '#0072bc',
						stack: 'Stack 1',
						datalabels: {
							formatter: function (value, ctx) {
								if(value !== 0) { return value; }
								else{ return ""; }
							},
							color: "white",
							align: "center",
							font: {
								weight: 'bold'
							}
						}
					}
				]
				};

				new Chart(ctx, {
					type: 'bar',
					data: data,
					options: {
						responsive: true,
						maintainAspectRatio: true,
						plugins: {
							title: {
								display: true,
								text: 'Comunicaciones Recibidas Gestionadas'
							},
						},
						scales: {
							x: {
								stacked: true,
								title: {
									text: 'Meses',
										color: '#00a651',
									display: true,
									font: {
										family: 'Comic Sans MS',
										size: 20,
										weight: 'bold',
										lineHeight: 1.2,
										},
										padding: {top: 20, left: 0, right: 0, bottom: 0}
								},
								border: {
									display: true
								},
								grid: {
									display: false,
									offset: true
								},
								ticks: {
									beginAtZero: true,
								}
							},
							y: {
								stacked: true,
								type: 'logarithmic',
								border: {
									display: true
								},
								grid: {
									display: false
								},
								ticks: {
									beginAtZero: true,
								}
							},
						}
					},
					plugins: [ChartDataLabels]
				});
				
				if ( $("#exportBtn04").length ) {
					// Funcion para exportar el grafico
					document.getElementById('exportBtn04').addEventListener('click', function() 
					{
						const link = document.createElement('a');
						link.href = document.getElementById('myChart19').toDataURL('image/png', 1.0); // 1.0 es la calidad de la imagen
						link.download = 'grafico04.png'; // Nombre del archivo
						link.click();
					});
				}
			}
			else if(tgraph == "LineChart")
			{
				const ctx = (element_chart == null || element_chart === "") ? document.getElementById('myChart21') : document.getElementById(element_chart);

				const data = {
					labels: dataviews['labels'],
					datasets: [
						{ 
							label: 'Comunicaciones Enviadas Gestionadas', 
							data: dataviews['total_gestionados'], 
							fill: true, 
							borderColor: 'rgb(231, 76, 60, 0.50)',
							backgroundColor: 'rgba(253, 200, 0, 0.25)',
							tension: 0.2,
							pointStyle: 'rectRounded',
							pointRadius: 10,
							pointHoverRadius: 15,
							pointBackgroundColor: '#00a651',
							datalabels: {								
								color: "#f3023a",
								align: "top",
								offset: 10,
								font: {
								  weight: 'bold',
								  size: 11
								}
							}
						},
						{ 
							label: 'Comunicaciones Enviadas Radicadas', 
							data: dataviews['total_radicadas'], 
							fill: true, 
							borderColor: 'rgb(170, 0, 0, 5.50)',
							backgroundColor: 'rgba(0, 166, 81, 0.25)',
							//backgroundColor: '#4bc0c0',
							tension: 0.2,
							pointStyle: 'rectRounded',
							pointRadius: 10,
							pointHoverRadius: 15,
							pointBackgroundColor: '#0072bc',
							datalabels: {								
								color: "#f35b02",
								align: "top",
								offset: 10,
								font: {
								  weight: 'bold',
								  size: 11
								}
							}
						}
					]
				}; 

				new Chart(ctx, {
					type: 'line',
					data: data,
					options: {
						responsive: true,
						radius: 10,
						scales: {
							x: {
								title: {
									text: 'Meses',
          							color: '#00a651',
									display: true,
									font: {
										family: 'Comic Sans MS',
										size: 20,
										weight: 'bold',
										lineHeight: 1.2,
									  },
									  padding: {top: 20, left: 0, right: 0, bottom: 0}
								},
								border: {
									display: true
								},
								ticks: {
									beginAtZero: true,
								}
							},
							y: {
								border: {
									display: true
								},
								ticks: {
									beginAtZero: true,
									stepSize: 1
								}
							},
						}
					},
					plugins: [ChartDataLabels]
				});
				
				if ( $("#exportBtn03").length ) {
					document.getElementById('exportBtn03').addEventListener('click', function() 
					{
						const link = document.createElement('a');
						link.href = document.getElementById('myChart21').toDataURL('image/png', 1.0); // 1.0 es la calidad de la imagen
						link.download = 'grafico03.png';
						link.click();
					});
				}
			}
			else if(tgraph == "LineChart2") 
			{
				// Obtener el elemento canvas
				const canvas = document.getElementById(element_chart);
				if (!canvas) {
					console.error('Canvas element not found:', element_chart);
					return;
				}
				
				const ctx = canvas.getContext('2d');
				
				const config = {
					type: 'line',
					data: {
						labels: dataviews['labels'],
						datasets: dataviews['totales']
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						
						// Configuración del layout para dar espacio a la leyenda
						layout: {
							padding: {
								bottom: 20  // Espacio adicional a la derecha si es necesario
							}
						},
						
						// Configuración de la leyenda
						plugins: {
							legend: {
								position: 'bottom',  // Posiciona la leyenda a la derecha
								align: 'start',     // Alinea las etiquetas al inicio (arriba)
								
								labels: {
									// Configuración visual de las etiquetas
									usePointStyle: true,    // Usa puntos en lugar de rectángulos
									pointStyle: 'circle',   // Estilo del punto
									padding: 15,            // Espaciado entre etiquetas
									font: {
										size: 12,
										family: 'Arial'
									}
									// Removí generateLabels personalizado para evitar conflictos
								}
							}
						},
						
						// Configuración de las escalas
						scales: {
							x: {
								display: true,
								title: {
									display: true,
									text: 'Meses'
								}
							},
							y: {
								display: true,
								title: {
									display: true,
									text: 'Valores'
								},
								ticks: {
									beginAtZero: true,
									stepSize: 1
								},
								beginAtZero: true
							}
						}
					}
		};
				const myChart = new Chart(ctx, config);
			}

		};

		$.MiMegaFuncion = function(array_dataviews)
		{
			array_dataviews.forEach(function(items)
			{
				if(items.hasOwnProperty('element_chart'))
					$.InitAllGraph(items.dataviews, items.tgraph, items.element_chart);
				else
					$.InitAllGraph(items.dataviews, items.tgraph);
			});
		}
	});
	
})(jQuery, window); 


function ColorCode()
{
	var makingColorCode = '0123456789ABCDEF';
	var finalCode = '#';
	for (var counter = 0; counter < 6; counter++) 
	{
		finalCode = finalCode + makingColorCode[Math.floor(Math.random() * 16)];
	}
	return finalCode; 
} 

function getRandomInt(min, max) 
{
	return Math.floor(Math.random() * (max - min + 1)) + min;
}