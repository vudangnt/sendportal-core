{{-- Example Email Templates Gallery --}}
<div class="card mb-4" id="example-templates-section">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-magic mr-1"></i> {{ __('Start from a Template') }}</span>
        <div>
            <input type="text" id="example-search" class="form-control form-control-sm d-inline-block" placeholder="{{ __('Search...') }}" style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary ml-1" id="toggle-examples">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="examples-body">
        {{-- Category filter --}}
        <div class="mb-3">
            <button class="btn btn-sm btn-primary example-filter active" data-category="all">{{ __('All') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="welcome">{{ __('Welcome') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="newsletter">{{ __('Newsletter') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="promotion">{{ __('Promotion') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="event">{{ __('Event') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="notification">{{ __('Notification') }}</button>
            <button class="btn btn-sm btn-outline-secondary example-filter" data-category="recruitment">{{ __('Recruitment') }}</button>
        </div>

        <div class="row" id="example-grid">
            {{-- 1. Blank Template --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="all">
                <div class="card h-100 example-card" data-template="blank" style="cursor:pointer;">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 160px; background: #f8f9fa; border: 2px dashed #dee2e6;">
                        <i class="fas fa-plus fa-2x text-muted mb-2"></i>
                        <small class="text-muted font-weight-bold">{{ __('Blank') }}</small>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Start from Scratch') }}</small>
                    </div>
                </div>
            </div>

            {{-- 2. Welcome Email --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="welcome">
                <div class="card h-100 example-card" data-template="welcome" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-hand-sparkles fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">WELCOME</div>
                            <div style="font-size:9px; opacity:0.8;">Thank you for joining</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Welcome Email') }}</small>
                    </div>
                </div>
            </div>

            {{-- 3. Newsletter --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="newsletter">
                <div class="card h-100 example-card" data-template="newsletter" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-newspaper fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">NEWSLETTER</div>
                            <div style="font-size:9px; opacity:0.8;">Weekly digest</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Newsletter') }}</small>
                    </div>
                </div>
            </div>

            {{-- 4. Product Launch --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
                <div class="card h-100 example-card" data-template="product_launch" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-rocket fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">NEW PRODUCT</div>
                            <div style="font-size:9px; opacity:0.8;">Launch announcement</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Product Launch') }}</small>
                    </div>
                </div>
            </div>

            {{-- 5. Sale / Promotion --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
                <div class="card h-100 example-card" data-template="sale_promotion" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-percentage fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">FLASH SALE</div>
                            <div style="font-size:9px; opacity:0.8;">Up to 50% OFF</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Sale Promotion') }}</small>
                    </div>
                </div>
            </div>

            {{-- 6. Event Invitation --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="event">
                <div class="card h-100 example-card" data-template="event_invitation" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-calendar-check fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">EVENT</div>
                            <div style="font-size:9px; opacity:0.8;">You're invited!</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Event Invitation') }}</small>
                    </div>
                </div>
            </div>

            {{-- 7. Thank You --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
                <div class="card h-100 example-card" data-template="thank_you" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-heart fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">THANK YOU</div>
                            <div style="font-size:9px; opacity:0.8;">We appreciate you</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Thank You') }}</small>
                    </div>
                </div>
            </div>

            {{-- 8. Re-engagement --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
                <div class="card h-100 example-card" data-template="re_engagement" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-undo fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">MISS YOU</div>
                            <div style="font-size:9px; opacity:0.8;">Come back!</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Re-engagement') }}</small>
                    </div>
                </div>
            </div>

            {{-- 9. Job Recruitment --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="recruitment">
                <div class="card h-100 example-card" data-template="recruitment" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #0c3483 0%, #a2b6df 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-briefcase fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">WE'RE HIRING</div>
                            <div style="font-size:9px; opacity:0.8;">Join our team</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Recruitment') }}</small>
                    </div>
                </div>
            </div>

            {{-- 10. Holiday Greeting --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="event">
                <div class="card h-100 example-card" data-template="holiday_greeting" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-gift fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">HAPPY HOLIDAYS</div>
                            <div style="font-size:9px; opacity:0.8;">Season's greetings</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Holiday Greeting') }}</small>
                    </div>
                </div>
            </div>

            {{-- 11. Simple Text --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="newsletter">
                <div class="card h-100 example-card" data-template="simple_text" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-align-left fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">SIMPLE TEXT</div>
                            <div style="font-size:9px; opacity:0.8;">Clean & minimal</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Simple Text') }}</small>
                    </div>
                </div>
            </div>

            {{-- 12. Announcement --}}
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
                <div class="card h-100 example-card" data-template="announcement" style="cursor:pointer;">
                    <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #536976 0%, #292E49 100%); display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-white p-2">
                            <i class="fas fa-bullhorn fa-2x mb-1"></i>
                            <div style="font-size:11px; font-weight:bold;">ANNOUNCEMENT</div>
                            <div style="font-size:9px; opacity:0.8;">Important update</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-1 text-center">
                        <small class="font-weight-bold">{{ __('Announcement') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .example-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 2px solid transparent;
    }
    .example-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    .example-card.selected {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.3);
    }
    .example-filter.active {
        background-color: #667eea;
        border-color: #667eea;
    }
</style>

<script>
var exampleTemplates = {
    blank: {
        counters: {u_row:1,u_column:1,u_content_text:1},
        body: {
            id: "blank",
            rows: [{
                id: "row1",
                cells: [1],
                columns: [{
                    id: "col1",
                    contents: [{
                        id: "text1",
                        type: "text",
                        values: {
                            containerPadding: "40px",
                            anchor: "",
                            fontSize: "14px",
                            textAlign: "center",
                            lineHeight: "150%",
                            linkStyle: {inherit:true,linkColor:"#0000ee",linkHoverColor:"#0000ee",linkUnderline:true,linkHoverUnderline:true},
                            hideDesktop: false,
                            displayCondition: null,
                            _meta: {htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},
                            selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,
                            text: "<p style=\"font-size: 14px; line-height: 150%;\"><span style=\"font-size: 18px; line-height: 27px;\">Start designing your email here...</span></p>"
                        },
                        hasDeprecatedFontControls: true
                    }],
                    values: {_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}
                }],
                values: {displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}
            }],
            headers:[],footers:[],
            values: {popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#000000",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#f5f5f5",preheaderText:"",linkStyle:{body:true,linkColor:"#0000ee",linkHoverColor:"#0000ee",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}
        },
        schemaVersion: 16
    },

    welcome: {
        counters:{u_row:4,u_column:4,u_content_text:4,u_content_button:1,u_content_divider:2,u_content_image:1},
        body:{
            id:"welcome_body",
            rows:[
                {id:"wr1",cells:[1],columns:[{id:"wc1",contents:[{id:"wi1",type:"image",values:{containerPadding:"30px 10px 10px",anchor:"",src:{url:"https://cdn.templates.unlayer.com/assets/1597218426105-xxxxc.png",width:140,height:140,maxWidth:"25%",autoWidth:false},textAlign:"center",altText:"Logo",action:{name:"web",values:{href:"",target:"_blank"}},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_image_1",htmlClassNames:"u_content_image"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false}}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#667eea",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"wr2",cells:[1],columns:[{id:"wc2",contents:[{id:"wt1",type:"text",values:{containerPadding:"10px 40px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#0000ee",linkHoverColor:"#0000ee",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Welcome to Our Community!</strong></span></p>"},hasDeprecatedFontControls:true},{id:"wt2",type:"text",values:{containerPadding:"0px 40px 20px",anchor:"",fontSize:"14px",color:"#e8e8e8",textAlign:"center",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\">We're thrilled to have you on board. Get ready to explore amazing features and exclusive content curated just for you.</p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_2",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#667eea",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_2",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"wr3",cells:[1],columns:[{id:"wc3",contents:[{id:"wt3",type:"text",values:{containerPadding:"30px 40px 10px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"170%",linkStyle:{inherit:true,linkColor:"#667eea",linkHoverColor:"#667eea",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 170%;\">Hi {{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Thank you for signing up! Here's what you can expect from us:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">✅ Weekly insights and tips<br>✅ Exclusive offers and promotions<br>✅ Early access to new features<br>✅ Helpful resources and guides</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">If you have any questions, feel free to reply to this email. We're here to help!</p>"},hasDeprecatedFontControls:true},{id:"wb1",type:"button",values:{containerPadding:"10px 40px 30px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#FFFFFF",backgroundColor:"#667eea",hoverColor:"#FFFFFF",hoverBackgroundColor:"#764ba2"},size:{autoWidth:true,width:"100%"},fontSize:"16px",textAlign:"center",lineHeight:"120%",padding:"14px 30px",border:{},borderRadius:"6px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Get Started</strong>",calculatedWidth:163,calculatedHeight:47},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"wr4",cells:[1],columns:[{id:"wc4",contents:[{id:"wt4",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_4",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company. All rights reserved.<br>You received this because you signed up on our website.<br><a href=\"{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_4",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_4",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}
            ],
            headers:[],footers:[],
            values:{popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#333333",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#e8e8e8",preheaderText:"Welcome aboard!",linkStyle:{body:true,linkColor:"#667eea",linkHoverColor:"#764ba2",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}
        },
        schemaVersion:16
    },

    newsletter: {
        counters:{u_row:5,u_column:6,u_content_text:7,u_content_button:2,u_content_divider:3,u_content_image:2},
        body:{
            id:"newsletter_body",
            rows:[
                {id:"nr1",cells:[1],columns:[{id:"nc1",contents:[{id:"nt1",type:"text",values:{containerPadding:"25px 40px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:false,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 24px; line-height: 33.6px;\"><strong>📬 Weekly Newsletter</strong></span></p><p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 13px; line-height: 18.2px;\">Your weekly dose of insights and updates</span></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5576c",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"nr2",cells:[1],columns:[{id:"nc2",contents:[{id:"nt2",type:"text",values:{containerPadding:"25px 40px 5px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><span style=\"font-size: 20px; line-height: 32px;\"><strong>🔥 Featured Article</strong></span></p><p style=\"font-size: 14px; line-height: 160%;\"><strong>10 Tips to Boost Your Email Marketing in 2025</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Discover proven strategies that top marketers use to increase open rates, click-throughs, and conversions. From subject line optimization to segmentation tactics...</p>"},hasDeprecatedFontControls:true},{id:"nb1",type:"button",values:{containerPadding:"5px 40px 20px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#FFFFFF",backgroundColor:"#f5576c",hoverColor:"#FFFFFF",hoverBackgroundColor:"#fa709a"},size:{autoWidth:true,width:"100%"},fontSize:"14px",textAlign:"left",lineHeight:"120%",padding:"10px 20px",border:{},borderRadius:"4px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Read More →</strong>",calculatedWidth:135,calculatedHeight:36},hasDeprecatedFontControls:true},{id:"nd1",type:"divider",values:{width:"100%",border:{borderTopWidth:"1px",borderTopStyle:"solid",borderTopColor:"#eeeeee"},textAlign:"center",containerPadding:"10px 40px",anchor:"",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_divider_1",htmlClassNames:"u_content_divider"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false}}],values:{_meta:{htmlID:"u_column_2",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_2",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"nr3",cells:[1,1],columns:[{id:"nc3",contents:[{id:"nt3",type:"text",values:{containerPadding:"15px 20px 15px 40px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><strong>📊 Industry Trends</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Latest market analysis and what it means for your business strategy going forward.</p><p style=\"font-size: 14px; line-height: 160%;\"><a href=\"#\">Read more →</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}},{id:"nc4",contents:[{id:"nt4",type:"text",values:{containerPadding:"15px 40px 15px 20px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_4",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><strong>💡 Quick Tips</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Simple actionable advice you can implement today to see immediate results.</p><p style=\"font-size: 14px; line-height: 160%;\"><a href=\"#\">Read more →</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_4",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
                {id:"nr5",cells:[1],columns:[{id:"nc6",contents:[{id:"nt7",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_7",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company | <a href=\"{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_6",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_5",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}
            ],
            headers:[],footers:[],
            values:{popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#333333",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#e8e8e8",preheaderText:"Your weekly newsletter",linkStyle:{body:true,linkColor:"#f5576c",linkHoverColor:"#fa709a",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}
        },
        schemaVersion:16
    },

    product_launch: JSON.parse(JSON.stringify({counters:{u_row:3,u_column:3,u_content_text:3,u_content_button:1},body:{id:"pl_body",rows:[{id:"pr1",cells:[1],columns:[{id:"pc1",contents:[{id:"pt1",type:"text",values:{containerPadding:"50px 40px 10px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>🚀 Introducing Our<br>Latest Innovation</strong></span></p>"},hasDeprecatedFontControls:true},{id:"pt2",type:"text",values:{containerPadding:"10px 40px 20px",anchor:"",fontSize:"14px",color:"#d4e5ff",textAlign:"center",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\">Experience the future of productivity with our brand new product. Designed to simplify your workflow and boost efficiency.</p>"},hasDeprecatedFontControls:true},{id:"pb1",type:"button",values:{containerPadding:"10px 40px 50px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#4facfe",backgroundColor:"#ffffff",hoverColor:"#FFFFFF",hoverBackgroundColor:"#4facfe"},size:{autoWidth:true,width:"100%"},fontSize:"16px",textAlign:"center",lineHeight:"120%",padding:"14px 30px",border:{},borderRadius:"30px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Learn More</strong>",calculatedWidth:152,calculatedHeight:47},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},{id:"pr3",cells:[1],columns:[{id:"pc3",contents:[{id:"pt3",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company | <a href=\"{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}],headers:[],footers:[],values:{popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#333333",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#4facfe",preheaderText:"Check out our new product!",linkStyle:{body:true,linkColor:"#4facfe",linkHoverColor:"#00f2fe",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}},schemaVersion:16}))
};

// Clone welcome as base for other templates with different colors/text
['sale_promotion','event_invitation','thank_you','re_engagement','recruitment','holiday_greeting','simple_text','announcement'].forEach(function(key) {
    if (!exampleTemplates[key]) {
        exampleTemplates[key] = JSON.parse(JSON.stringify(exampleTemplates.welcome));
    }
});

// Customize Sale Promotion
exampleTemplates.sale_promotion.body.rows[0].values.columnsBackgroundColor = "#fa709a";
exampleTemplates.sale_promotion.body.rows[1].values.columnsBackgroundColor = "#fa709a";
exampleTemplates.sale_promotion.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>🔥 FLASH SALE</strong></span></p><p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 20px; line-height: 28px;\">Up to 50% OFF Everything!</span></p>";
exampleTemplates.sale_promotion.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Don't miss out on our biggest sale of the year. Limited time only! Shop your favorites at unbeatable prices.</p>";
exampleTemplates.sale_promotion.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#fa709a",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f5576c"};
exampleTemplates.sale_promotion.body.rows[2].columns[0].contents[1].values.text = "<strong>Shop Now →</strong>";
exampleTemplates.sale_promotion.body.values.backgroundColor = "#fee140";

// Customize Event Invitation
exampleTemplates.event_invitation.body.rows[0].values.columnsBackgroundColor = "#a18cd1";
exampleTemplates.event_invitation.body.rows[1].values.columnsBackgroundColor = "#a18cd1";
exampleTemplates.event_invitation.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>📅 You're Invited!</strong></span></p>";
exampleTemplates.event_invitation.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Join us for an exclusive event. Meet industry leaders, network with peers, and discover new opportunities.</p>";
exampleTemplates.event_invitation.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"><strong>Event Details:</strong></p><p style=\"font-size: 14px; line-height: 170%;\">📍 Location: Conference Center<br>🗓️ Date: TBD<br>⏰ Time: 9:00 AM - 5:00 PM<br>👔 Dress Code: Business Casual</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Space is limited. Reserve your spot today!</p>";
exampleTemplates.event_invitation.body.rows[2].columns[0].contents[1].values.text = "<strong>RSVP Now</strong>";
exampleTemplates.event_invitation.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#a18cd1",hoverColor:"#FFFFFF",hoverBackgroundColor:"#764ba2"};

// Customize Thank You
exampleTemplates.thank_you.body.rows[0].values.columnsBackgroundColor = "#43e97b";
exampleTemplates.thank_you.body.rows[1].values.columnsBackgroundColor = "#43e97b";
exampleTemplates.thank_you.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>❤️ Thank You!</strong></span></p>";
exampleTemplates.thank_you.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Your order has been confirmed. We appreciate your business and trust.</p>";
exampleTemplates.thank_you.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Hi {{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Thank you for your recent purchase! Your order is being processed and you'll receive a shipping confirmation shortly.</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">If you have any questions about your order, please don't hesitate to contact us.</p>";
exampleTemplates.thank_you.body.rows[2].columns[0].contents[1].values.text = "<strong>Track Order</strong>";
exampleTemplates.thank_you.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#43e97b",hoverColor:"#FFFFFF",hoverBackgroundColor:"#38f9d7"};

// Customize Re-engagement
exampleTemplates.re_engagement.body.rows[0].values.columnsBackgroundColor = "#f6d365";
exampleTemplates.re_engagement.body.rows[1].values.columnsBackgroundColor = "#f6d365";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>👋 We Miss You!</strong></span></p>";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[0].values.color = "#333333";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">It's been a while since we last saw you. Come back and see what's new!</p>";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[1].values.color = "#555555";
exampleTemplates.re_engagement.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Hi {{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">We noticed you haven't visited us in a while. Here's what you've been missing:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">🆕 New features and improvements<br>🎁 Exclusive comeback offer just for you<br>📚 Fresh content and resources</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">As a special welcome back gift, enjoy 20% off your next purchase!</p>";
exampleTemplates.re_engagement.body.rows[2].columns[0].contents[1].values.text = "<strong>Come Back & Save 20%</strong>";
exampleTemplates.re_engagement.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#fda085",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f6d365"};

// Customize Recruitment
exampleTemplates.recruitment.body.rows[0].values.columnsBackgroundColor = "#0c3483";
exampleTemplates.recruitment.body.rows[1].values.columnsBackgroundColor = "#0c3483";
exampleTemplates.recruitment.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>💼 We're Hiring!</strong></span></p>";
exampleTemplates.recruitment.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Join our growing team and build the future with us. We offer competitive benefits and an amazing culture.</p>";
exampleTemplates.recruitment.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"><strong>Open Positions:</strong></p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">🔹 Senior Software Engineer<br>🔹 Product Manager<br>🔹 UX/UI Designer<br>🔹 Marketing Specialist<br>🔹 Data Analyst</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Why join us?</strong></p><p style=\"font-size: 14px; line-height: 170%;\">✅ Remote-first culture<br>✅ Competitive salary<br>✅ Health & wellness benefits<br>✅ Professional development budget</p>";
exampleTemplates.recruitment.body.rows[2].columns[0].contents[1].values.text = "<strong>View Open Positions</strong>";
exampleTemplates.recruitment.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#0c3483",hoverColor:"#FFFFFF",hoverBackgroundColor:"#a2b6df"};

// Customize Holiday
exampleTemplates.holiday_greeting.body.rows[0].values.columnsBackgroundColor = "#ff0844";
exampleTemplates.holiday_greeting.body.rows[1].values.columnsBackgroundColor = "#ff0844";
exampleTemplates.holiday_greeting.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>🎄 Happy Holidays!</strong></span></p>";
exampleTemplates.holiday_greeting.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Wishing you joy, peace, and wonderful moments with your loved ones this holiday season.</p>";
exampleTemplates.holiday_greeting.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear {{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">As this wonderful year comes to a close, we want to express our heartfelt gratitude for your continued support and trust.</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">🎁 As a token of appreciation, enjoy a special holiday gift from us!</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Wishing you and your family a magical holiday season! 🌟</p>";
exampleTemplates.holiday_greeting.body.rows[2].columns[0].contents[1].values.text = "<strong>Claim Your Gift 🎁</strong>";
exampleTemplates.holiday_greeting.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#ff0844",hoverColor:"#FFFFFF",hoverBackgroundColor:"#ffb199"};
exampleTemplates.holiday_greeting.body.values.backgroundColor = "#ffb199";

// Customize Simple Text
exampleTemplates.simple_text.body.rows[0].values.columnsBackgroundColor = "#2c3e50";
exampleTemplates.simple_text.body.rows[1].values.columnsBackgroundColor = "#2c3e50";
exampleTemplates.simple_text.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 24px; line-height: 33.6px;\"><strong>Your Company Name</strong></span></p>";
exampleTemplates.simple_text.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Clean, professional communication</p>";
exampleTemplates.simple_text.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 180%;\">Hi {{first_name}},</p><p style=\"font-size: 14px; line-height: 180%;\"> </p><p style=\"font-size: 14px; line-height: 180%;\">This is a clean, text-focused email template perfect for professional communications, updates, and announcements.</p><p style=\"font-size: 14px; line-height: 180%;\"> </p><p style=\"font-size: 14px; line-height: 180%;\">The minimal design ensures your message is the focus, with no distractions. Customize the text, colors, and add your branding elements.</p><p style=\"font-size: 14px; line-height: 180%;\"> </p><p style=\"font-size: 14px; line-height: 180%;\">Best regards,<br>Your Team</p>";
exampleTemplates.simple_text.body.rows[2].columns[0].contents[1].values.text = "<strong>Visit Our Website</strong>";
exampleTemplates.simple_text.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2c3e50",hoverColor:"#FFFFFF",hoverBackgroundColor:"#4ca1af"};

// Customize Announcement
exampleTemplates.announcement.body.rows[0].values.columnsBackgroundColor = "#292E49";
exampleTemplates.announcement.body.rows[1].values.columnsBackgroundColor = "#292E49";
exampleTemplates.announcement.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>📢 Important Announcement</strong></span></p>";
exampleTemplates.announcement.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">We have exciting news to share with you. Read on for the details.</p>";
exampleTemplates.announcement.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear {{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">We're excited to announce some important changes that will improve your experience with us:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">🔹 <strong>New Feature Launch</strong> — Enhanced dashboard with real-time analytics<br>🔹 <strong>Improved Performance</strong> — 3x faster loading times<br>🔹 <strong>Better Security</strong> — Advanced encryption and 2FA support<br>🔹 <strong>New Integrations</strong> — Connect with 50+ new tools</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">These updates will be rolled out over the next few weeks. Stay tuned!</p>";
exampleTemplates.announcement.body.rows[2].columns[0].contents[1].values.text = "<strong>Learn More</strong>";
exampleTemplates.announcement.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#292E49",hoverColor:"#FFFFFF",hoverBackgroundColor:"#536976"};
</script>
