 <div id="tabs-2" class="tab">
        <form id="packagingFormNew">
            <div class="row single-column-row">
                <div  class="packContainer">
                    <div class="column single-column">
                        <div class="field">
                            <label for="namePkg" class="labeldiv">Name of Packaging:</label>  
                            <input id="namePkg" name ="namePkg" type="text" class="required"/>

                        </div>
                    </div>
                    <div class="column single-column">
                        <div class="field">
                            <label for="desc" class="labeldiv">Description:</label>  
                            <input id="desc" name ="desc" type="text"/>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row single-column-row">
                <div  id ="existTypeContainer" class="packContainer">
                    <div class="column single-column">
                        <div class="field">
                            <label for="typeOp" class="labeldiv">Type :</label>  
                            <select name="typeOp" id="typeOp" class="notZero"> 
                                    <option value=0>Choose 
                                    <?=$typeOptions?> 
                            </select>
                            <a id ="newTypeDefLink"  title="Add new type" class="shopifine-ui-icon-link">
                                <span class="ui-icon ui-icon-plus"></span>
                            </a>

                        </div>

                    </div>
                </div>
            </div>
            <div class="row single-column-row" >
                <div id="existingUOMContainerTabTwo">
                    <div class="column single-column">
                        <div class="field">
                            <label for="uomOp-TabTwo" class="labeldiv">Choose Unit</label>  
                            <select name="uomOp" id="uomOp-TabTwo" class="uomOp"> 
                                    <option value=0>Choose 
                                    <?=$uomOptions?> 
                                </select>
                        </div>
                    </div>
                    <div class="column single-column">
                        <div class="field">
                            <label for="sizeOp-TabTwo"class="labeldiv" >Choose Size</label>  
                            <select name="sizeOp" id="sizeOp-TabTwo"> 
                            </select>
                        </div>   
                    </div>
                </div>

            </div>

            <div class="row single-column-row" style="display:none">
                <div id="newUOMContainerTabTwo">
                    <div class="column single-column">
                        <div class="field">
                            <label for="uomIpTabTwo" class="labeldiv">Define Unit : </label>  
                            <input id="uomIpTabTwo" name ="uomIp" type="text" class="required"/>
                        </div>
                    </div>
                    <div class="column single-column">
                        <div class="field">
                            <label for="denomIpTabtwo" class="labeldiv">Denomination : </label>  
                            <input id ="denomIpTabtwo" name ="denomIp" type="text" class="required"/>
                        </div>   
                    </div>
                </div>
            </div>
            <div class="row single-column-row">
            <div class="column single-column" id="newMeasurementTypeLinkContainerTabTwo">

                    <h6> Didn't Find The Right Unit of Measurement? </h6>
                    <a id="newMeasurementTypeLinkTabTwo" href="#">Define A New Measurement Unit</a>
                </div>

            </div>
            <div class="row single-column-row" style="display:none">
                <div class="column single-column" id="existingMeasurementTypeLinkContainerTabTwo">

                    <h6> Want to check Existing Measurement Types </h6>
                    <a id="existMeasurementTypeLinkTabTwo" href="#">Show Existing Measurement Types</a>
                </div>

            </div>
        </form>
        <div class="ui-dialog-buttonpane">
        <div class="shopifine-ui-dialog-buttonset">
            <button id="newPkgBtn" type="button" class="shopifine-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                <span class="ui-button-text">Create Package</span>
        </div> 
    </div>
</div>