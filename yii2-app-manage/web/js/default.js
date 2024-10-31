var options = {
  series: [{
    name: "Desktops",
    data: [10, 35, 15, 78, 40, 60, 12, 60],
}],
  chart: {
  type: 'area',
  height: 120,
  offsetY: 50,
  zoom: {
    enabled: false
  },
  toolbar: {
    show: false,
  }, 
  dropShadow: {
    enabled: true,
    top: 5,
    left: 0,
    bottom: 3,
    blur: 2,
    color: TokyoAdminConfig.secondary,
    opacity: 0.2,
  },
},
colors: [TokyoAdminConfig.secondary],
fill: {
  type: "gradient",
  gradient: {
    shadeIntensity: 1,
    opacityFrom: 0.6,
    opacityTo: 0.2,
    stops: [0, 100, 100]
  }
},
dataLabels: {
  enabled: false
},
grid: {
  show: false,
},
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: 'straight',
    width: 2,
  },
  tooltip: {
      custom: function ({ series, seriesIndex, dataPointIndex,}) {
        return '<div class="apex-tooltip p-2">' + '<span>' + '<span class="bg-primary">' + '</span>' + 'customer' + '<h3>' + '$'+ series[seriesIndex][dataPointIndex] + '<h3/>'  + '</span>' + '</div>';
      },
    },
};
var chart = new ApexCharts(document.querySelector("#earning-chart"), options);
chart.render();

var options = {
  series: [{
    name: 'Revenue',
    data: [92, 64, 43, 80, 58, 92, 46, 76, 80]
  }, {
    name: 'Revenue',
    data: [20, 48, 69, 32, 54, 20, 66, 36, 32],
  },
],
chart: {
  type: 'bar',
  offsetY: 55,
  toolbar: {
    show: false
  },
  height: 100,
  stacked: true,
},
 states: {          
  hover: {
    filter: {
      type: 'darken',
      value: 1,
    }
  }           
},
plotOptions: {
  bar: {
    horizontal: false,
    s̶t̶a̶r̶t̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
    e̶n̶d̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
    borderRadius: 3,
    columnWidth: '55%',
  }
},  
dataLabels: {
  enabled: false
},
grid: {
  yaxis: {
    lines: {
        show: false
    }
  },
},
xaxis: {
  labels: {
    show: false,
  },
  axisBorder: {
    show: false,
  },
  axisTicks: {
    show: false,
  },
},
yaxis: {
  show: false,
  dataLabels: {
    enabled: true
  },
},
fill: {
  opacity: 1,
  colors: [TokyoAdminConfig.primary, '#eaf1ff']
},
legend: {
  show: false
},
tooltip: {
  custom: function ({ series, seriesIndex, dataPointIndex,}) {
    return '<div class="apex-tooltip p-2">' + '<span>' + '<span class="bg-primary">' + '</span>' + 'Revenue' + '<h3>' + '$'+ series[seriesIndex][dataPointIndex] + '<h3/>'  + '</span>' + '</div>';
  },
},
};
var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
chart.render();

  var options = {
    series: [{
    name: 'TEAM A',
    type: 'column',
    data: [220,, 250, , 210, , 210, , 270, , 220, , 250, , 260, , 210, , 230]
  },{
    name: 'TEAM B',
    type: 'area',
    data: [210,170, 240, 160, 200, 170, 200, 150, 260, 170, 210,170,240, 160, 250, 140, 200, 140,220,220]
  }],
    chart: {
    height: 332,
    type: 'area',
    stacked: false,
    toolbar: {
      show: false,
    }
  },
  stroke: {
    width: [0, 2, 5],
    curve: 'stepline'
  },
  plotOptions: {
    bar: {
      columnWidth: '100px'
    }
  },
  colors: [ '#bebebe' , TokyoAdminConfig.primary],
  dropShadow: {
    enabled: true,
    top: 5,
    left: 6,
    bottom: 5,
    blur: 2,
    color: TokyoAdminConfig.primary,
    opacity: 0.5,
  },
  fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.5,
        opacityTo: 0.1,
        stops: [0, 90, 100]
      }
    },    
  grid :{
    show: true,
    strokeDashArray: 3,
    xaxis: {
      lines: {
        show: true,
      }
    },
    yaxis: {
      lines: {
        show: true,
      }
    },
  },
  xaxis: {
    categories: ["Jan", "", "feb", "", "Mar", "", "Apr", "", "May", "", "Jun" ,"" , "July" , "" , "Aug" , "" , "Sep" , "" , "Oct" , ""],
    labels: {
      style: {
          fontFamily: 'Outfit, sans-serif',
          fontWeight: 500,
          colors: '#8D8D8D',
      },
    },
    axisBorder: {
      show: false
    },
  },
  dataLabels: {
    enabled: false,
  },
    yaxis: {
      labels: {
        style: {
            fontFamily: 'Outfit, sans-serif',
            fontWeight: 500,
            colors: '#8D8D8D',
        },
      },
    },
  legend:{
    show: false,
  },
  tooltip: {
    custom: function ({ series, seriesIndex, dataPointIndex,}) {
      return '<div class="apex-tooltip p-2">' + '<span>' + '<span class="bg-primary">' + '</span>' + 'Project Created' + '<h3>' + '$'+ series[seriesIndex][dataPointIndex] + '<h3/>'  + '</span>' + '</div>';
    },
  },
  };
  var chart = new ApexCharts(document.querySelector("#revenuegrowth"), options);
  chart.render();


  const userPosition = {
    series: [{
      data: [70, 30, 40, 90, 60, 50],
    },],
    chart: {
      type: 'bar',
      height: 290,
      width: '100%',
      toolbar: {
        show: false,
      },
    },
    colors: ['rgba(39, 72, 134, 0.2)', 'rgba(211, 77, 63, 0.2)', 'rgba(218, 152, 23, 0.2)', 'rgba(14, 164, 186, 0.1)', 'rgba(71, 148, 71, 0.2)' , 'rgba(214, 76, 89, 0.2)'],
    fill: {
      opacity: 0.4,
    },
    plotOptions: {
      bar: {
        borderRadius: 0,
        horizontal: true,
        distributed: true,
        barHeight: '30%',
        dataLabels: {
          position: 'top',
        },
      },
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val;
      },
      background: {
        enabled: true,
        foreColor: '#fff',
        borderRadius: 5,
        padding: 4,
        opacity: 0.9,
        borderWidth: 1,
        borderColor: '#fff',
      },
      style: {
        fontSize: '12px',
        colors: ['#f2f2f2'],
      },
    },
    legend: {
      show: false,
    },
  
    grid: {
      show: true,
      borderColor: '#f2f2f2',
      strokeDashArray: 0,
      position: 'back',
      xaxis: {
        lines: {
          show: true,
        },
      },
      yaxis: {
        lines: {
          show: false,
        },
      },
    },
  
    yaxis: {
      labels: {
        show: false,
      },
    },
    xaxis: {
      categories: ['United States', 'Russia', 'Australia', 'Germany', 'Africa', 'France'],
      logBase: 100,
      tickAmount: 10,
      min: 0,
      max: 100,
      labels: {
        minHeight: undefined,
        maxHeight: 18,
        offsetX: -5,
        offsetY: 0,
        tooltip: {
          enabled: false,
        },
      },
      style: {
        fontSize: "13px",
        colors: "#959595",
        fontFamily: "Lexend, sans-serif",
      },
      axisBorder: {
        show: false
      },
      title: {
        offsetX: 0,
        offsetY: -28,
        style: {
          fontSize: "13px",
          colors: "#959595",
          fontFamily: "Lexend, sans-serif",
        },
      },
    },
    tooltip: {
      custom: function ({ series, seriesIndex, dataPointIndex,}) {
        return '<div class="apex-tooltip p-2">' + '<span>' + '<span class="bg-primary">' + '</span>' + 'United States' + '<h3>' + '$'+ series[seriesIndex][dataPointIndex] + '<h3/>'  + '</span>' + '</div>';
      },
    },
    responsive: [{
      breakpoint: 675,
      options: {
        chart: {
          height: 300,
          offsetY: 15,
        },
        xaxis: {
          title: {
            offsetY: 0,
          },
        },
  
        grid: {
          padding: {
            left: -13,
            bottom: 25,
          },
        },
      },
    },],
  };
  
  var userPositionEl = new ApexCharts(document.querySelector('#userPosition'), userPosition);
  userPositionEl.render();

  var options = {
    series: [76, 67, 61, 90],
    chart: {
      height: 310,
      type: "radialBar",
    },
    plotOptions: {
      radialBar: {
        offsetY: 0,
        startAngle: 0,
        endAngle: 270,
        hollow: {
          margin: 5,
          size: "30%",
          background: "transparent",
          image: undefined,
        },
        dataLabels: {
          name: {
            fontSize: "22px",
          },
          value: {
            fontSize: "16px",
          },
          total: {
            show: true,
            label: "Total",
            formatter: function (w) {
              return 249;
            },
          },
        },
        track: {
          background: "var(--recent-chart-bg)",
        },
      },
    },
    colors: [
      TokyoAdminConfig.primary,
      TokyoAdminConfig.secondary,
      "#da9817",
      "#479447",
    ],
    labels: ["Vimeo", "Messenger", "Facebook", "LinkedIn"],
    legend: {
      labels: {
        useSeriesColors: true,
      },
      markers: {
        size: 0,
      },
      formatter: function (seriesName, opts) {
        return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex];
      },
      itemMargin: {
        vertical: 2,
      },
    },
    responsive: [
      {
        breakpoint: 1711,
        options: {
          chart: {
            height: 280,
          },
        },
      },
      {
        breakpoint: 1592,
        options: {
          chart: {
            height: 250,
            offsetY: 20,
          },
        },
      },
      {
        breakpoint: 1481,
        options: {
          chart: {
            height: 270,
          },
        },
      },
      {
        breakpoint: 1300,
        options: {
          chart: {
            height: 230,
          },
        },
      },
    ],
  };
  var chart = new ApexCharts(document.querySelector("#investment"), options);
  chart.render();