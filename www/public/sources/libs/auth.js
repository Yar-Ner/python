import {Utils} from "./utils";

export class Auth {
  constructor() {
    const token = Utils.getUrlParam("token")
    if (token) {
      let expire = new Date(new Date().setDate(new Date().getDate() + 30));
      webix.storage.cookie.put('token', token, null, expire)
    }
    this.token =  webix.storage.cookie.get("token");
    this.userId = 0;
    this.name = 'unauth';
    this.fullname = 'Не авторизован';
    this.instance_id = 'unregistered';
    this.tokenFromGet = null;
  }

  authUser(cb) {
    var me = this;
    webix.ui({
      view: "window",
      id: "loginWindow",
      css: "login_window",
      fullscreen:true,
      borderless: true,
      modal: true,
      move: true,
      head: {},
      body: {
        id: "loginForm",
        view: "form",
        borderless: true,
        elements: [
          {
            rows: [
                {},
                {},
                { 
                  cols: [
                    {},
                    {   
                      width: 430, height: 160, id: "logobox", css: "logobox", align:"middle", borderless: true,
                      template: "<img src='../sources/styles/logo.svg' valign='middle' style=\"width: 100%\">"
                    },
                    {}
                  ]
                },
                {},
                {
                  cols: [
                    {},
                    {},
                    { 
                      view: "text", label: "Логин", value: "", name: "login", invalidMessage: "Укажите логин", 
                      id: "auth_login", height: 96, width: 390, inputWidth: 390, inputHeight: 60, css: "input_sign input_login",
                      on: {
                        onFocus: function (o) {
                          webix.html.addCss($$("auth_login").getNode(), "selected");
                        }
                      }
                    },
                    {},
                    {},
                  ]
                },
                {
                  cols: [
                    {},
                    {},
                    { 
                      css: "input_sign ",
                      cols:[
                        {
                          css: "input_sign input_password",
                          view: "text", type: "password", label: "Пароль", name: "password", invalidMessage: "Укажите пароль", 
                          id: "auth_password", height: 96, width: 390, inputWidth: 390, inputHeight: 60, 
                          on: {
                            onFocus: function (o) {
                              webix.html.addCss($$("auth_password").getNode(), "selected");
                            }
                          }
                        },
                      ]
                    },
                    {},
                    {}
                  ]
                },
                {
                  cols: [
                    { view:"label", label: "", inputWidth: 390, height: 70, align:"center", id: "message", css:"login_message"},
                  ]
                },
                {
                  cols: [
                    {},
                    {},
                    {  view: "button", value: "Войти", hotkey: "enter", id: "auth_enter", height: 86, width: 390,
                    click: function () {
                      var form = $$('loginForm');
                      form.disable();
                      var obj = form.getValues();
                      webix.ajax().post("/auth/login",obj, {
                        error: function (text, data, XmlHttpRequest) {
                          form.enable();
                          webix.message({ type: "error", text: 'Неверный логин или пароль.'});
                        },
                        success: function (text, data, XmlHttpRequest) {
                          var jd = data.json();
                          form.enable();
                          if ($$("loginWindow")) $$("loginWindow").close();
                          me.token = jd.token;
                          let expire = new Date(new Date().setDate(new Date().getDate() + 30));
                          webix.storage.cookie.put("token", jd.token, null, expire);
                          me.check(cb);
                        }
                      });
                    } },
                    {},
                    {}
                  ]
                },
                {},
                {},
                {}
            ]
          },
        ]
      },
      on: {
        onShow: function () {
        },
      }
    }).show();
  };

  check(cb) {
    var me = this;
    if (!this.token && !webix.storage.cookie.get("token") && !me.tokenFromGet) {
      return this.authUser(cb);      
    }
    webix.ajax().get("/auth/info", {"token":me.tokenFromGet}, {
      error: function (text, data, XmlHttpRequest) {
        webix.message({ type: "error", text: 'Произошла ошбика сервера.'})
        me.authUser(cb);
      },
      success: function (text, data, XmlHttpRequest) {
        var jd = data.json();
        if (me.tokenFromGet/* && !webix.storage.cookie.get("token")*/) {
          let expire = new Date(new Date().setDate(new Date().getDate() + 30));
          webix.storage.cookie.put("token", me.tokenFromGet, null, expire);
        }
        me.roles = jd['rules'];
        me.name = jd['name'];
        me.fullname = jd['fullname'];
        me.instance_id = jd['instance'];
        me.userId = jd.id
        if (jd.id) {
          try {
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
              OneSignal.init({
                appId: "4c650135-21fb-471c-a5cc-b19438898286",
                notifyButton: {
                  enable: true,
                },
              });
              OneSignal.sendTag("user_id", jd.id);
              if (webix.storage.cookie.get('allowedNotify') == null && !(navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/))) {
                let allowedNotify = 0;
                let expire = new Date(new Date().setDate(new Date().getDate() + 30));

                webix.confirm({
                  title:"Разрешите отправлять Вам сообщнения, для этого подпишитесь на уведомления",
                  buttons:["Подписаться", "Нет, спасибо"],
                  width:500,
                })
                .then(function(result){
                  if (result) {
                    allowedNotify = 1;
                    OneSignal.showNativePrompt();
                  }
                  webix.storage.cookie.put("allowedNotify", allowedNotify, null, expire);
                })
                .fail(function(){
                  webix.storage.cookie.put("allowedNotify", allowedNotify, null, expire);
                })
              }
            });
          } catch (e) {
            console.error("Fail to init OneSignal", e);
          }
        }
        if (cb) cb();
      }
    });
  }

  logout(cb) {
    var me = this;
    webix.ajax().post("/auth/logout", {
      error: function (text, data, XmlHttpRequest) {
        webix.message({ type: "error", text: text});
      },
      success: function (text, data, XmlHttpRequest) {
        var jd = data.json();        
        me.token = 0;
        webix.storage.cookie.put("token", '');
        document.location.reload();
      }
    });
  }

  settings(cb) {
    var me = this;
  }

  getFullname() {
    return this.fullname;
  }
  
  isAllowedRoles(roles) {
    if (webix.auth && webix.auth.roles) {
      return webix.auth.roles.filter((item)=>{ return roles.includes(item); }).length;
    }
    
    return true;
  }

}