<html>
    <head>
       <?php $this->load->view("common/header"); ?>
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        <script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.jstree.js"></script>
        <style type="text/css">
        * { font-family: Verdana; font-size: 96%; }
        label { width: 10em; float: left; }
        label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
        p { clear: both; }
        .submit { margin-left: 12em; }
        em { font-weight: bold; padding-right: 1em; vertical-align: top; }
        .field {
    clear:both;
}
        </style>
        <script>
            $(document).ready(function(){
                 $("#treeViewDiv")
                 .jstree({
        "plugins" : ["themes", "json_data", "ui"],
        "json_data" : {
            "ajax" : {
                "type": 'GET',
                "url": function (node) {
                    var nodeId = "";
                    var url = ""
                    if (node == -1)
                    {
                        url = "index.php/utilities/renderParents";
                    }
                    else
                    {
                        nodeId = node.attr('id');
                        url = "index.php/utilities/renderChildren";
                    }

                    return url;
                },
                data : function(node) {
                    if (node != -1){
                        return {

                          "nodeid":$.trim(node.attr('id'))
                        }
                    }
            },
                "success": function (new_data) {
                    return new_data;
                }
            }
        }})
//                 .jstree({
//                  "json_data" : {
//            "ajax" : {
//                "type": 'GET',
//                "url": function (node) {
//                    var nodeId = "";
//                    var url = ""
//                    if (node == -1)
//                    {
//                        url = "http://localhost/introspection/introspection/product/";
//                    }
//                    else
//                    {
//                        nodeId = node.attr('id');
//                        url = "http://localhost/introspection/introspection/children/" + nodeId;
//                    }
//
//                    return url;
//                },
//                "success": function (new_data) {
//                    return new_data;
//                }
//            }  
 //                 },
                 
//                    "json_data" : {
//                        "data":<?php echo $treedata ?>,
//                        "ajax" : {
//                            "url" : "index.php/utilities/renderChildren"
////                            data : function(n) {
////                                     return {
////                                       "nodeid":$.trim(n.attr('id'))
////                                     }
////                                   }
//                        }
//                    },
//                    
//                    "plugins" : [ "themes", "json_data", "ui" ]
//"json_data" : {
    //"data":<?php echo $treedata ?>,
   // "data":[{"data":"Furniture","attr":{"id":"10"},"state":"closed"},{"data":"Electronics","attr":{"id":"13"},"state":"closed"},{"data":"Apparel","attr":{"id":"18"},"state":"closed"},{"data":"Household Items","attr":{"id":"20"},"state":"closed"},{"data":"Home","attr":{"id":"36"},"state":"closed"}],
//			"data" : [
//				{ 
//					"data" : "A node", 
//					"state" : "closed"
//				},
//{"data":"Furniture","attr":{"id":"10"},"state":"closed"},
//				{ 
//					"attr" : { "id" : "li.node.id3" }, 
//					"data" : { 
//						"title" : "Long format demo", 
//						"attr" : { "href" : "#" } 
//					} 
//				}
//			],
//			"ajax" : { "url" : "index.php/utilities/renderChildren" }
//		},
//		"plugins" : [ "themes", "json_data" ]
//                })
                    $("#test").click(function(){console.log($('#treeViewDiv').jstree('get_selected').data('id'));
})
    $("#testattr").click(function(){console.log($('#treeViewDiv').jstree('get_selected').attr('id'));
                    })

            });
        </script>
    </head>
     
    <body>
         <?php $this->load->view("common/menubar"); ?>
        <div id="treeViewDiv">
        </div>

<button id="test">test</button>
<button id="testattr">test Attr</button>
​
<!--        <div id="categoryForm" class="utilityform">
        <form action="index.php/utilities/createCategory" method="post" id="deliverypointform">
            <fieldset>
            <div>
               
                
               
                   
            
            </div>
                </fieldset>
        </form>
</div>-->
        <?php $this->load->view("partial/footer"); ?>
    </body>
    
   
</html>
 

 