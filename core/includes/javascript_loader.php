<?php

#   ___           __ _           _ __    __     _     
#  / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__  
# / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \ 
#/ /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
#\____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/ 
#
#		-[ Created by �Nomsoft
#		  `-[ Original core by Anthony (Aka. CraftedDev)
#
#				-CraftedWeb Generation II-                  
#			 __                           __ _   							   
#		  /\ \ \___  _ __ ___  ___  ___  / _| |_ 							   
#		 /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|							   
#		/ /\  / (_) | | | | | \__ \ (_) |  _| |_ 							   
#		\_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -	   
#                  The policy of Nomsoftware states: Releasing our software   
#                  or any other files are protected. You cannot re-release    
#                  anywhere unless you were given permission.                 
#                  � Nomsoftware 'Nomsoft' 2011-2012. All rights reserved.    
?>
<script type="text/javascript" src="core/javascript/jquery.js"></script>
<script type="text/javascript" src="core/javascript/main.js"></script>

<?php

####SHOP CARTS####
    if ( $_GET['page'] == "donateshop" )
    { ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                loadMiniCart("donateCart");
            });
        </script><?php
    }

    if ( $_GET['page'] == "voteshop" )
    { ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                loadMiniCart("voteCart");
            });
        </script><?php
    }

    if ( DATA['use']['slideshow'] == TRUE )
    { ?>
        <script type="text/javascript" src="core/javascript/slideshow.js"></script><?php
    }

    if ( DATA['website']['expansion'] > 2 )
    {
        echo '<script type="text/javascript" src="http://static.wowhead.com/widgets/power.js"></script>';
    }
    else
    {
        echo '<script type="text/javascript" src="http://cdn.cavernoftime.com/api/tooltip.js"></script>';
    }

####CURSOR TRACKER####
    if ( $_GET['page'] == "donateshop" || $_GET['page'] == "voteshop" )
    { ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                $(document).mousemove(function (e)
                {
                    mouseY = e.pageY;
                });
            });
        </script><?php
    }

####FACEBOOK####
    if ( DATA['social']['facebook_module'] == true )
    { ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                var box_width_one = $(".box_one").width();
                $("#fb").attr("width", box_width_one);
            });
        </script> <?php
    }

####SERVER STATUS######
    if ( DATA['website']['server_status']['enable'] == true )
    { ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                $.post("core/includes/scripts/misc.php", 
                    {serverStatus: true},
                    function (data)
                    {
                        $("#server_status").html(data);
                        $(".srv_status_po").hover(function ()
                        {
                            $(".srv_status_text").fadeIn("fast");
                        },
                        function ()
                        {
                            $(".srv_status_text").fadeOut("fast");
                        });
                    });
            });
        </script><?php
    }
global $Plugins;
$Plugins->load('javascript');