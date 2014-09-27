            <div class="row single-column-row" >
                            <div class="existingUOMContainer">
                                <div class="column single-column">
                                    <div class="field">
                                        <label for="uomOpExist">Choose Unit</label>  
                                        <select name="uomOp"> 
                                                <option value=0>Choose 
                                                <?=$uomOptions?> 
                                            </select>
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label >Choose Size</label>  
                                        <select name="sizeOp"> 
                                        </select>
                                    </div>   
                                </div>
                            </div>

                        </div>

                        <div class="row single-column-row" style="display:none">
                            <div class="newUOMContainer">
                                <div class="column single-column">
                                    <div class="field">
                                        <label>Define Unit : </label>  
                                        <input  name ="uomIp" type="text" class="required"/>
                                    </div>
                                </div>
                                <div class="column single-column">
                                    <div class="field">
                                        <label>Denomination : </label>  
                                        <input name ="denomIp" type="text" class="required"/>
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div class="row single-column-row">
                        <div class="column single-column" class="newMeasurementTypeLinkContainer">
        
                                <h6> Didn't Find The Right Unit of Measurement? </h6>
                                <a class="newMeasurementTypeLink" href="#">Define A New Measurement Unit</a>
                            </div>

                        </div>
                        <div class="row single-column-row" style="display:none">
                            <div class="column single-column" class="existingMeasurementTypeLinkContainer">
       
                                <h6> Want to check Existing Measurement Types </h6>
                                <a class="existMeasurementTypeLink" href="#">Show Existing Measurement Types</a>
                            </div>

                        </div>