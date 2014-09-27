<div id="tabs-1" class="tab">
        <form id="packagingForm">
            <div class="row single-column-row">
                <div id="packDiv">
                    <div class="column single-column">
                        <div class="field">
                            <label for="packageOptions" class="labeldiv">Available Package Types:</label>  
                            <select name="packageOptions" id ="packageOptions" class="opt required"> 
                                    <option value=0>Choose 
                                    <?=$options?> 
                            </select>
                        </div>
                    </div>
                    <div class="column single-column" style="display:none"> 
                        <div id="typeCon"  class="field">
                            <label for="packtype" class="labeldiv">Package Type: </label>  
                            <input id="packtype" name ="packtype" type="text"/>  
                        </div>
                    </div>
                </div>
            </div>

            <div class="row single-column-row" >
                <div id="existingUOMContainerTabOne">
                    <div class="column single-column">
                        <div class="field">
                            <label for="uomOp-TabOne" class="labeldiv">Choose Unit</label>  
                            <select name="uomOp" id="uomOp-TabOne" class="uomOp"> 
                                    <option value=0>Choose 
                                    <?=$uomOptions?> 
                                </select>
                        </div>
                    </div>
                    <div class="column single-column">
                        <div class="field">
                            <label for="sizeOp-TabOne" class="labeldiv">Choose Size</label>  
                            <select name="sizeOp" id="sizeOp-TabOne" > 
                            </select>
                        </div>   
                    </div>
                </div>

            </div>

            <div class="row single-column-row" style="display:none">
                <div id="newUOMContainerTabOne">
                    <div class="column single-column">
                        <div class="field">
                            <label for="uomIpTabOne" class="labeldiv">Define Unit : </label>  
                            <input id="uomIpTabOne" name ="uomIp" type="text" class="required"/>
                        </div>
                    </div>
                    <div class="column single-column">
                        <div class="field">
                            <label for="denomIpTabOne" class="labeldiv"> Denomination : </label>  
                            <input id ="denomIpTabOne" name ="denomIp" type="text" class="required"/>
                        </div>   
                    </div>
                </div>
            </div>
            <div class="row single-column-row">
            <div class="column single-column" id="newMeasurementTypeLinkContainerTabOne">

                    <h6> Didn't Find The Right Unit of Measurement? </h6>
                    <a id="newMeasurementTypeLinkTabOne" href="#">Define A New Measurement Unit</a>
                </div>

            </div>
            <div class="row single-column-row" style="display:none">
                <div class="column single-column" id="existingMeasurementTypeLinkContainerTabOne">

                    <h6> Want to check Existing Measurement Types </h6>
                    <a id="existMeasurementTypeLinkTabOne" href="#">Show Existing Measurement Types</a>
                </div>

            </div>


        </form>
        <div class="ui-dialog-buttonpane">
        <div class="shopifine-ui-dialog-buttonset">
            <button id="existingPkgBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                <span class="ui-button-text">Create Package</span>
        </div> 
    </div>
</div>