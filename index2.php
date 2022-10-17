<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
        Ext.onReady(function(){
            /** 
            * =======================================
            * Define Function
            * =======================================
            **/
            function forgotPassword(){
                windowPassword.show();   
            }
            /** 
            * =======================================
            * Define Field
            * =======================================
            **/
            var userId = new Ext.form.TextField({
				fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'USER ID',
                name: 'userId',
                allowBlank: false,
                maxLength : 8,
                enforceMaxLength : 8,
                //maskRe: /\d/,
                listeners: {
                    'render': function(c) {
                        c.getEl().on('keypress', function(e) {
                            if(e.getKey() == 13) //atau Ext.EventObject.ENTER = e.ENTER
                            password.focus();
                        }, c);
                    }  
                }
            });
            var password = new Ext.form.TextField({
                fieldLabel: 'PASSWORD',
                name: 'password', 
                inputType: 'password',
                allowBlank: false,
                maxLength: 20,
                enforceMaxLength: 20,
                enableKeyEvents: true
            });
            var label = new Ext.form.field.Display({
                fieldLabel: '',
                value: '<span id="inf">* Please enter your email address. You will receive a new password via email</span>',
                padding: '0 0 12 10'
            });
            var userId2 = new Ext.form.TextField({
                fieldLabel: 'USER ID',
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                maxLength : 8,
                enforceMaxLength :8,
                labelAlign: 'right',
                labelWidth: 80,
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                }
            });
            var email = new Ext.form.TextField({
                vtype: 'email',
                fieldLabel: 'EMAIL',
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelAlign: 'right',
                labelWidth: 80
            });
            /** 
            * =======================================
            * Define Form
            * =======================================
            **/
            var forgotPassForm = Ext.widget('form',{
                bodyPadding: 5,
                frame: true,
                items: [label,userId2,email],
                buttons: [{
                    text: 'OK',
                    handler: function(){
                        if(forgotPassForm.getForm().isValid()) {
                            Ext.Ajax.request({
                                url:'db/Login/Get_Password.php',
                                params: {
                                    userIdVal   : Ext.String.trim(userId2.getValue()),
                                    mailVal     : Ext.String.trim(email.getValue())
                                },
                                success: function(response){
                                    var answer=Ext.decode(response.responseText).msg.message;
                                    if(answer=='UserIdNotExist'){
                                        Ext.MessageBox.show({
                                            title:'Message',
                                            msg:'Please check your user id.',
                                            buttons:Ext.Msg.OK,
                                            icon:Ext.MessageBox.ERROR
                                        });
                                    }if(answer=='incorrect'){
                                        Ext.MessageBox.show({
                                            title:'Message',
                                            msg:'Please check your user id and email.',
                                            buttons:Ext.Msg.OK,
                                            icon:Ext.MessageBox.ERROR
                                        });
                                    }else{
                                        if(answer=='success_reset'){
                                            Ext.Msg.alert('Message','Your new password has been sent to your email.');
                                            windowPassword.hide();
                                            userId2.reset();
                                            email.reset();
                                        }
                                    }
                                }
                            });
                        }
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        windowPassword.hide();
                        email.reset();
                    }
                }]
            });
            var loginForm = Ext.create('Ext.form.Panel',{
                frame: true,
                defaultType: 'textfield',
                bodyPadding: 10,
                height: 190,
                fieldDefaults: {
                    msgTarget: 'side',
                    anchor: '90%'
                },
                items:[
                    userId,password,{
                    xtype: 'box',
                    autoEl: {
                        tag: 'a',
                        href: '#',
                        cls: 'a',
                        cn: '<span>* Forgot Password</span>'
                    },
                    listeners: {
                        render: function(component) {
                            component.getEl().on('click', function(e) {
                                forgotPassword();
                            });    
                        }
                    }
                }],
                buttons:[{
                    text: 'LOG IN',
                    type: 'Submit',
                    id: 'loginButton',
                    handler: function(){
                        if(loginForm.getForm().isValid()){
							var msgBoxCheck = Ext.MessageBox.show({
                                msg: 'Checking your data, please wait...',
                                progressText: 'Connecting...',
                                id: 'check-msgbox',
                                width: 300,
                                wait: true,
                                waitConfig: {interval:200}
                            });
                            Ext.Ajax.request({
                                method: 'POST',
                                url:'db/Login/Login.php',
                                waitTitle: 'Connecting',
                                waitMsg: 'Checking Data',
                                params: {
                                    userId: userId.getValue(),
                                    password: password.getValue()
                                },
                                success: function(response){
                                    var answer=Ext.decode(response.responseText).msg.message;
                                    if(answer=='Exist'){
                                        window.location='WCOM002.php';
                                    }
                                    else if(answer=='UserNotExist'){
                                        Ext.MessageBox.show({
                                            title:'Message',
                                            msg:'Please check your user id or password.',
                                            buttons:Ext.Msg.OK,
                                            icon:Ext.MessageBox.ERROR,
                                            closable: false
                                        });
                                    }
                                    else if(answer=='UserInactive'){
                                        Ext.MessageBox.show({
                                            title:'Message',
                                            msg:'User ID is inactive. Please contact Administrator to activate.',
                                            buttons:Ext.Msg.OK,
                                            icon:Ext.MessageBox.ERROR,
                                            closable: false
                                        });
                                    }
                                    else if(answer=='EmailNotExist'){
                                        Ext.MessageBox.show({
                                            title:'Message',
                                            msg:'User Id does not have an email.',
                                            buttons:Ext.Msg.OK,
                                            icon:Ext.MessageBox.ERROR,
                                            closable: false
                                        });
                                    }
                                    else{
                                        window.location='index.php';
                                    }
                                    userId.reset();
                                    password.reset();
                                }
                            });
                        }
                    }
                },{
                    text: 'CANCEL',
                    handler: function(){
                        loginForm.getForm().reset();
                    }
                }]
            });
            /** 
            * =======================================
            * Define Window
            * =======================================
            **/
            var windowPassword  = Ext.widget('window',{
                title: 'FORGOT PASSWORD',
                closeAction: 'hide',
                closable: false,
                resizable: false,
                draggable: false,
                border: false,
                modal: true,
                height: 183,
                width: 410,
                items: [forgotPassForm]
            });
            var windowLogin = new Ext.Window ({
                title: 'E-PURCHASE SYSTEM',
                closable: false,
                resizable: false,
                draggable: false,
                border: false,
                height: 245,
                width: 380,
                bbar: [{
                    xtype: 'tbtext',
                    text: 'Copyright © 2018 PT. TD AUTOMOTIVE COMPRESSOR INDONESIA'
                }],
                items: [loginForm]
            });
            windowLogin.show();
        });
        </script>
    </head>
    <body>
    </body>
</html>
