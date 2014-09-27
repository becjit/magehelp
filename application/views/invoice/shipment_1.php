<html>
    <head>
       <?php $this->load->view("common/header"); ?> 
       <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/shopifine_table.css"/>

    </head>
    <body onload="setPackedInvoices()">
        <?php $this->load->view("common/menubar"); ?>
        <form id ="shipmentForm" method="post" action="index.php/invoice/confirmation">
            <div>
                <li>
                    <label for ="deliveryPointDD"> Delivery Point</label>
                    <select name="deliveryPointDD"> 
                    <option value=0>Choose 
                    <?=$options?> 
                    </select>
                </li>
                <li>
                    <label for ="deliveryVehicleDD"> Delivery Vehicle</label>
                    <select name="deliveryVehicleDD"> 
                    <option value=0>Choose 
                    <?=$optionsVehicle?> 
                    </select> 
                </li>
             </div>
            
            <br/>
            <br/>
            <label for="shipmentTable" class="shipmentHeading">Select To Ship</label>
            <table id="shipmentTable" name ="shipmentTable">
            
            <thead>        
                <tr>
                    <th>
                        <input type="checkbox" class="checkAll"/>
                    </th>
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
            <input type="submit" value="Confirm"/>
            <input type="hidden" id="invoiceIDsJSON" name ="invoiceIDsJSON" value =""/>
        </form>
    </body>
</html>

<script type="text/javascript">
    
function setPackedInvoices(){
            
            var   invoiceList = jQuery.parseJSON('<?php echo json_encode($invoiceListSession )?>');
            var count = '<?php echo sizeof($invoiceListSession) ?>';
            var matched;
            //alert (count);
            
            for  (i=0; i<count ; i++){
                if (invoiceList[i].isMatched){
                    matched = "Invoiced and Packed Items did not match";
                }
                else{
                    matched = "Invoiced and Packed Items matched";
                }
                $("#shipmentTable > tbody tr:last").after(
                    "<tr class=" + "invoiceRow" + ">\n\
                        <td><input type=\"checkbox\" /></td>\n\
                        <td class=" + "invoiceIdCol"+ ">" + invoiceList[i].invoiceId + "</td>\n\
                        <td>"+ matched + "</td>\n\
                        <td>"+ invoiceList[i].comments + "</td>\n\
                        <td>" + "" + "</td>\n\
                    </tr>");
            }
        };  

                    $("#shipmentForm").submit(function(){
                        var jsonArray = [];
                        $("#shipmentTable > tbody tr.invoiceRow").each(function(){
                            var $checkBx = $(this).find('input[type=checkbox]');
                            var isChecked = $checkBx[0].checked;
                            if (isChecked)
                                jsonArray.push({invoice_id:$(this).find('.invoiceIdCol').html().trim()})
                            
                        })
                        //alert(jsonArray.length);
                        if (jsonArray.length == 0){
                            return false;
                        }
                        $("#invoiceIDsJSON").val(JSON.stringify(jsonArray));
                    });
</script>