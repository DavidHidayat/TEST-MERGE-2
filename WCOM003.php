<?
session_start(); 
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
    document.location="index.php"; </script>
<?
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>EPS</title>
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css"></link>
        <link rel="stylesheet" type="text/css" href="css/eps.css"></link>
        <!--  Ext Js library -->
        <script type="text/javascript" src="extjs/bootstrap.js"></script>
        <script>
        if (Ext.BLANK_IMAGE_URL.substr(0, 5) != 'data:') {
            Ext.BLANK_IMAGE_URL = 'extjs/resources/images/default/s.gif';
        }
        Ext.QuickTips.init();
        var mainLayout = function(){
            var userId = '<? echo $sNPK; ?>';
            var toolbarTop = new Ext.Toolbar ({
                id: 'toolbar', 
                items: [{
                    xtype: 'buttongroup',
                    title: 'Miscellaneous',
                    items: [{
                        xtype: 'button',
                        text: 'Main Screen',
                        handler: function(){
                            window.location='WCOM002.php'
                        }   
                    },{
                        xtype: 'button',
                        text: 'Change Password',
                        handler: function(){
                            window.location='WCOM003.php'
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
                                window.location='db/Login/Logout.php';
                            }
                        })
                    }
                }]
            });
            /** 
            * =======================================
            * Define Function
            * =======================================
            **/
            function changePassword(){
                if (WCOM003Form.getForm().isValid()) {
                    Ext.Msg.confirm('Confirm', 'Are you sure want to change your password?', function(btn, text){
                        if (btn == 'yes'){
                            Ext.Ajax.request({
                                url: 'db/Master_Data/EPS_M_USER.php?action=updatePassword',
                                method: 'POST',
                                params: {
                                    oldPassword: oldPassword.getValue(),
                                    newPassword: newPassword.getValue()
                                },
                                success: function(response){
                                    var answer=Ext.decode(response.responseText).message;
                                    if(answer == 'Error'){
                                        Ext.Msg.alert('Message','Incorrect old password.');
                                        oldPassword.reset();
                                    }else{
                                        Ext.MessageBox.alert('Status','Change password succeed.');
                                        window.location='db/Login/Logout.php';
                                    }
                                }
                            });
                        }
                    });
                }
            }
            /** 
            * =======================================
            * Define Field
            * =======================================
            **/
            var oldPassword = new Ext.form.TextField({
                fieldLabel: 'Old Password',
                inputType: 'password',
                maxLength: 20,
                enforceMaxLength: 20,
                allowBlank:false,
                regex: /[a-zA-Z0-9]+/
            });
            var newPassword = new Ext.form.TextField({
                fieldLabel: 'New Password', 
                id: 'NewPass',
                inputType: 'password',
                maxLength: 20,
                enforceMaxLength: 20, 
                allowBlank:false,
                regex: /[a-zA-Z0-9]+/
            });
            var newPasswordConf = new Ext.form.TextField({
                fieldLabel: 'Confirm New Password',
                inputType: 'password', 
                vtype: 'password', 
                initialPassField: 'NewPass',    // id of the initial password field
                maxLength: 20,
                enforceMaxLength: 20 ,
                allowBlank:false,
                regex: /[a-zA-Z0-9]+/
            });
            // Check validation for new password mattch or not
            Ext.apply(Ext.form.field.VTypes, {
                password: function(val, field) {
                    if (field.initialPassField) {
                        var passform  = field.up('form').down('#' + field.initialPassField);
                        return (val == passform .getValue());
                    }
                    return true;
                },
                passwordText: 'Passwords do not match'
            });
            var WCOM003Form = Ext.create('Ext.form.Panel', { 
                title: 'Change Password',
                border: false,
                frame: false,
                bodyPadding: 15, 
                fieldDefaults: {
                    labelWidth: 130,
                    msgTarget: 'side',
                    anchor: '40%'
                },
                items:[oldPassword,newPassword,newPasswordConf],
                buttons:[{
                    text: 'Update',
                    handler: function(){
                        changePassword();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        this.up('form').getForm().reset();
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        window.location='WCOM002.php'
                    }
                }]
            });
            Ext.create('Ext.Viewport',{
                layout: 'border', 
                padding: '5',
                items: [{
                    region: 'north',
                    split: true, 
                    border: false, 
                    items: [toolbarTop]
                },{
                    region: 'center',
                    id: 'content', 
                    height: 200,
                    width: 200,
                    items: [WCOM003Form]
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