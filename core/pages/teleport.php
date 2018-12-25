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
<?php
    global $Account, $Website, $Database;
    $Account->isNotLoggedIn();
?>
<div class='box_two_title'>Character Teleport</div>
Choose the character & desired location you wish to teleport.
<?php
    $service = "teleport";

    if ($GLOBALS['service'][$service]['price'] == 0)
        echo '<span class="attention">Teleportation is free of charge.</span>';
    else
    {
        ?>
        <span class="attention">Teleportation costs 
            <?php echo $GLOBALS['service'][$service]['price'] . ' ' . $Website->convertCurrency($GLOBALS['service'][$service]['currency']); ?></span>
            <?php
        if ($GLOBALS['service'][$service]['currency'] == "vp")
            echo "<span class='currency'>Vote Points: " . $Account->loadVP($_SESSION['cw_user']) . "</span>";
        elseif ($GLOBALS['service'][$service]['currency'] == "dp")
            echo "<span class='currency'>" . $GLOBALS['donation']['coins_name'] . ": " . $Account->loadDP($_SESSION['cw_user']) . "</span>";
    }
?>
<hr/>
<h3 id="choosechar">Choose Character</h3> 
<?php
    $Database->selectDB("webdb", $conn);
    $result = $Database->select("realms", "char_db, name", null, null, "ORDER BY id ASC;")->get_result();
    while ($row = $result->fetch_assoc())
    {
        $acct_id = $Account->getAccountID($_SESSION['cw_user']);
        $realm   = $row['name'];
        $char_db = $row['char_db'];

        $Database->selectDB($char_db);
        $result = $Database->select("characters", "name, guid, gender, class, race, level, online", null, "account=". $acct_id .";")->get_result();
        while ($row = $result->fetch_assoc())
        {
            ?>
            <div class='charBox' style="cursor:pointer;" id="<?php echo $row['guid'] . '*' . $char_db; ?>"<?php if ($row['online'] != 1)
            { ?> 
                     onclick="selectChar('<?php echo $row['guid'] . '*' . $char_db; ?>', this)"<?php } ?>>
                <table>
                    <tr>
                        <td>
                            <?php
                            if (!file_exists('styles/global/images/portraits/' . $row['gender'] . '-' . $row['race'] . '-' . $row['class'] . '.gif'))
                                echo '<img src="styles/' . $GLOBALS['template']['path'] . '/images/unknown.png" />';
                            else
                            {
                                ?>
                                <img src="styles/global/images/portraits/<?php echo $row['gender'] . '-' . $row['race'] . '-' . $row['class']; ?>.gif" border="none"><?php } ?>
                        </td>

                        <td>
                            <h3><?php echo $row['name']; ?></h3>Level <?php echo $row['level'] . " " . $Character->getRace($row['race']) . " " . $Character->getGender($row['gender']) ." " . $Character->getClass($row['class']);?><br/>
                            Realm: <?php echo $realm; ?>
                            <?php if ($row['online'] == 1)
                                echo "<br/><span class='red_text'>Please log out before trying to teleport.</span>";
                            ?>
                        </td>
                    </tr>                         
                </table>
            </div>  
        <?php } ?>
        <br/>&nbsp;
        <span id="teleport_to" style="display:none;">  

        </span>               
        <?php
    }
?>