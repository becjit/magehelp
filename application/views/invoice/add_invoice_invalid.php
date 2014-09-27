<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
        <style type="text/css">
        * { font-family: Verdana; font-size: 96%; }
        label { width: 10em; float: left; }
        label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
        p { clear: both; }
        .submit { margin-left: 12em; }
        em { font-weight: bold; padding-right: 1em; vertical-align: top; }
        </style>
        <script>
            $(document).ready(function(){
                $("#invoiceform").validate();
            });
        </script>
    </head>
     
    <body onload="setInvoiceStatus()">
         <?php $this->load->view("common/menubar"); ?>
        <form action="index.php/invoice" method="post" id="invoiceform">
            <label for="invoice_number">Invoice Number :</label>  
            <input id="invoice_number" name ="invoice_number" type="text" class="required"/>            
            <input type="submit" value="Submit" />
        </form>
        <table id="invoiceTable" style="border:1">
            
            <thead>        
                <tr>
                    
                    <th>
                        Invoice Number
                    </th>
                    <th>
                        Matched
                    </th>
                    <th>
                        Comments
                    </th>
                    <th>
                        Done By
                    </th>
                </tr>
            </thead>           
            <tbody>
                <tr/>
            </tbody>
        </table>
        <?php
        // put your code here
        ?>
    </body>
    
   
</html>
 

 <script type="text/javascript">
        function setInvoiceStatus(){
            
            var   invoiceList = jQuery.parseJSON('<?php echo json_encode($invoiceListSession)?>');
            var count = '<?php echo sizeof($invoiceListSession) ?>';
            alert (count);
            
            for  (i=0; i<count ; i++){
                $("#invoiceTable > tbody tr:last").after(
                    "<tr class=" + "invoiceRow" + ">\n\
                        <td>"+ invoiceList[i].invoiceId + "</td>\n\
                        <td>"+ invoiceList[i].isMatched + "</td>\n\
                        <td>"+ invoiceList[i].comments + "</td>\n\
                        <td>" + "" + "</td>\n\
                    </tr>");
            }
        }; 
        ("#invoiceform").submit(function(){
            var invoiceId = ("#invoice_number")
            
        });
        
    </script>