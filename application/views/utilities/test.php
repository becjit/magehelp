<html>
    <head>
       <meta http-equiv="content-type" content="text/html; charset=utf-8" />
       <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
       <script src="<?php echo base_url();?>js/chosen.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--       <link rel="stylesheet" rev="stylesheet" href="<?php //echo base_url();?>css/ospos.css" />-->
<!--        <link rel="stylesheet" rev="stylesheet" href="<?php //echo base_url();?>css/shopifine.css" />-->
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/chosen.css" />	
       
    </head>
    <body>
        <form method="POST" action="testSelect">
           
<select style="width:300px;"name ="test[]" multiple="multiple" class="chzn-select">
  <option value="1">Volvo</option>
  <option value="2">Saab</option>
  <option value="3">Opel</option>
  <option value="4">Audi</option>
</select>
<input type="submit"/>
​​​​​​​​
        </form>
    </body>
</html>
<script>
    $(".chzn-select").chosen();
    </script>