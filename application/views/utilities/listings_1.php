<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>js/jqplot/jquery.jqplot.css" />
        <script type="text/javascript" src="<?php echo base_url();?>js/jqplot/jquery.jqplot.min.js"></script>
        <!--<script type="text/javascript" src="<?php echo base_url();?>js/jqplot/plugins/jquery.jqplot.min.js"></script>-->
        <script type="text/javascript" src="<?php echo base_url();?>js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
         <!--<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/shopifine/message.css" />-->
         <style type="text/css">
             .menu_item {
                 display: inline;
             }
        </style>
        <style type="text/css">
            #menubar_admin_navigation {
                top:49px;
            }
            #content_area {
                left:18px;
                width:98%
            }
            html {
                overflow-x:hidden;
            }
        </style>
        <script>
            $(document).ready(function(){
    var s1 = [200, 600, 700, 1000];
    var s2 = [460, -210, 690, 820];
    var s3 = [-260, -440, 320, 200];
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    var ticks = ['May', 'June', 'July', 'August'];
     
    var plot1 = $.jqplot('chart1', [s1, s2, s3], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true}
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
        series:[
            {label:'Hotel'},
            {label:'Event Regristration'},
            {label:'Airfare'}
        ],
        // Show the legend and put it outside the grid, but inside the
        // plot container, shrinking the grid to accomodate the legend.
        // A value of "outside" would not shrink the grid and allow
        // the legend to overflow the container.
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
                pad: 1.05,
                tickOptions: {formatString: '$%d'}
            }
        }
    });
});
      </script>
      </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <div id="chart1" style="height:400px;width:600px; "></div>​
<!--        <div>
            <ul>
                <li>
                    <a href="index.php/utilities/loadMfrModel">Create Manufacturer or Model</a>
                </li>
                <br/>
                <li>
                    <a href="index.php/utilities/loadDeliveryPoint">Create Delivery Point</a>
                </li>
                <br/>
                <li>
                    <a href="index.php/utilities/loadMeasurementUnit">Create Units of Measurement</a>
                </li>
                <br/>
                <li>
                    <a href="index.php/utilities/loadPackage">Create Package</a>
                </li>
                 
            </ul>
        </div>-->
        <?php $this->load->view("partial/footer"); ?>
        
    </body>
</html>
