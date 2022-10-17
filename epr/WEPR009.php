<?php session_start(); 
if(isset($_SESSION['sNPK']))
{       
    $sNPK       = $_SESSION['sNPK'];
    $sNama      = $_SESSION['sNama'];
    $sBunit     = $_SESSION['sBunit'];
    $sSeksi     = $_SESSION['sSeksi'];
    $sKdper     = $_SESSION['sKdper'];
    $sNmPer     = $_SESSION['sNmper'];
    $sKdPlant   = $_SESSION['sKDPL'];
    $sNmPlant   = $_SESSION['sNMPL'];
    $sRoleId    = $_SESSION['sRoleId'];
    $sInet      = $_SESSION['sinet'];
    $sNotes     = $_SESSION['snotes'];
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page!");
    document.location="../index.php"; </script>
<?
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>EPS</title>
        <!--  Ext Js library -->
        <script type="text/javascript" src="../extjs/bootstrap.js"></script>
        <link rel="stylesheet" type="text/css" href="../extjs/resources/css/ext-all.css"></link>
        
        <script>
        if (Ext.BLANK_IMAGE_URL.substr(0, 5) != 'data:') {
            Ext.BLANK_IMAGE_URL = '../extjs/resources/images/default/s.gif';
        }
        Ext.QuickTips.init();
        var mainLayout = function(){
            var roleIdLogin = '<?php echo $_SESSION['sRoleId'];?>';
            var toolbarTop = new Ext.Toolbar ({
                id: 'toolbar', 
                items: [{
                    xtype: 'buttongroup',
                    title: 'Miscellaneous',
                    items: [{
                        xtype: 'button',
                        text: 'Main Screen',
                        handler: function(){
                            Ext.Msg.alert('Message','Sorry, you cannot change menu during in Create PR Screen. <br>Please choose action "Send or Save or Cancel Create PR".');
                        }   
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'PR',
                    items: [{
                        xtype: 'button',
                        text: 'PR List',
                        handler: function(){
                            window.location='WEPR001.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PR Waiting',
                        handler: function(){
                            window.location='../epr_/WEPR013.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Create New PR',
                        handler: function(){
                            window.location='WEPR002.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Upload PR',
                        handler: function(){
                            window.location='WEPR008.php'
                        }
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'Search',
                    items: [{
                        xtype: 'button',
                        text: 'PR Search',
                        handler: function(){
                            
                            window.location='../epr_/WEPR090.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PO Search',
                        handler: function(){
                            if(roleIdLogin == "ROLE_03" || roleIdLogin == "ROLE_08")
                            {
                                window.location='../epr_/WEPR091.php'
                            }
                            else
                            {
                                Ext.Msg.alert('Message','Sorry, you are not auhotrized to access this menu.');
                            }
                        }
                    }]
                },'->',
                {
                    xtype: 'tbtext', //Logged is as:
                    text: '<h2>Welcome, <?php echo stripslashes(addslashes($sNama)); ?></h2>#USER ID: <?php echo $sUserId; ?> #BU: <?php echo $sBuLogin?>'
                },'-',{
                    xtype: 'button',
                    text: 'Logout',
                    handler:function(){  
                        Ext.Msg.confirm('Confirm', 'Do you want to Log out?', function(btn, text){
                            if (btn == 'yes'){
                                window.location='../db/Login/Logout.php';
                            }
                        })
                    }
                }]
            });
            var panelCenter = Ext.create('Ext.panel.Panel', {
                border: false
            });
            var mainView = new Ext.create('Ext.Viewport',{
                layout: 'border', 
                padding: '5',
                items: [{
                    region: 'north',
                    split:true, 
                    border:false, 
                    items: [toolbarTop]
                },{
                    region: 'center',
                    id: 'content', 
                    layout: 'fit',
                    items: [panelCenter]
                }],
                renderTo: Ext.getBody()
            });
        }
        Ext.onReady(mainLayout);
        </script>
    </head>
    <body>
    </body>
</html>