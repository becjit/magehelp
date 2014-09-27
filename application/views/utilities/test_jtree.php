<!DOCTYPE html>
<html>
<head>
    <title>Demo</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="<?php echo base_url();?>js/jstree/jquery.jstree.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#selector").jstree({
                "plugins": ["themes","html_data"]
            });
        });
    </script>
</head>
<body>
    <div id="selector">
        <ul>
            <li><a>Team A's Projects</a>
                <ul>
                    <li><a>Iteration 1</a>
                        <ul>
                            <li><a>Story A</a></li>
                            <li><a>Story B</a></li>
                            <li><a>Story C</a></li>
                        </ul>
                    </li>
                    <li><a>Iteration 2</a>
                        <ul>
                            <li><a>Story D</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</body>
</html>