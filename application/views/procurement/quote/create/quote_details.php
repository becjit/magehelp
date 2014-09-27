<div id="details">
    <div class="row">
                        
        <div class="column base-column">
            <div class="field">
                <label for="supplierOp">Supplier:</label>  
                <select name="supplierOp" id ="supplierOp" class="required"> 
                    <option value="">Choose 
                        <?= $supplierOptions ?> 
                </select>

            </div>
        </div>
        <div class="column base-column">
            <div class="field">
                <label for="warehouseOp">Warehouse:</label>  
                <select name="warehouseOp" id ="warehouseOp"> 
                    <option value="">Choose 
                        <?= $warehouseOptions ?> 
                </select>

            </div>

        </div>
        <div class="column base-column" style="width:35em;">
            <div class="field">
                <label for="reqdate">Request By Date:</label>  
                <input id="reqdate" name ="reqdate" type="text" class="dateValidate" style="width:12em;"/>
            </div>
        </div>

    </div>

    <input id="quoteId" name ="quoteId" type="hidden" value=""/>


</div>
              

                   
                 
          