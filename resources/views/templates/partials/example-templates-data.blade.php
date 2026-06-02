{{-- Example Templates JavaScript Data --}}
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
 {id:"wr1",cells:[1],columns:[{id:"wc1",contents:[{id:"wi1",type:"image",values:{containerPadding:"30px 10px 10px",anchor:"",src:{url:"https://placehold.co/140x40.png?text=Your+Logo",width:140,height:140,maxWidth:"25%",autoWidth:false},textAlign:"center",altText:"Logo",action:{name:"web",values:{href:"",target:"_blank"}},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_image_1",htmlClassNames:"u_content_image"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false}}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#667eea",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"wr2",cells:[1],columns:[{id:"wc2",contents:[{id:"wt1",type:"text",values:{containerPadding:"10px 40px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#0000ee",linkHoverColor:"#0000ee",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Welcome to Our Community!</strong></span></p>"},hasDeprecatedFontControls:true},{id:"wt2",type:"text",values:{containerPadding:"0px 40px 20px",anchor:"",fontSize:"14px",color:"#e8e8e8",textAlign:"center",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\">We're thrilled to have you on board. Get ready to explore amazing features and exclusive content curated just for you.</p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_2",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#667eea",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_2",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"wr3",cells:[1],columns:[{id:"wc3",contents:[{id:"wt3",type:"text",values:{containerPadding:"30px 40px 10px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"170%",linkStyle:{inherit:true,linkColor:"#667eea",linkHoverColor:"#667eea",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 170%;\">Hi @{{first_name}},</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Thank you for signing up! Here's what you can expect from us:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Weekly insights and tips<br>Exclusive offers and promotions<br>Early access to new features<br>Helpful resources and guides</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">If you have any questions, feel free to reply to this email. We're here to help!</p>"},hasDeprecatedFontControls:true},{id:"wb1",type:"button",values:{containerPadding:"10px 40px 30px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#FFFFFF",backgroundColor:"#667eea",hoverColor:"#FFFFFF",hoverBackgroundColor:"#764ba2"},size:{autoWidth:true,width:"100%"},fontSize:"16px",textAlign:"center",lineHeight:"120%",padding:"14px 30px",border:{},borderRadius:"6px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Get Started</strong>",calculatedWidth:163,calculatedHeight:47},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"wr4",cells:[1],columns:[{id:"wc4",contents:[{id:"wt4",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_4",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company. All rights reserved.<br>You received this because you signed up on our website.<br><a href=\"@{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_4",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_4",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}
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
 {id:"nr1",cells:[1],columns:[{id:"nc1",contents:[{id:"nt1",type:"text",values:{containerPadding:"25px 40px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:false,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 24px; line-height: 33.6px;\"><strong>Weekly Newsletter</strong></span></p><p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 13px; line-height: 18.2px;\">Your weekly dose of insights and updates</span></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5576c",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"nr2",cells:[1],columns:[{id:"nc2",contents:[{id:"nt2",type:"text",values:{containerPadding:"25px 40px 5px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><span style=\"font-size: 20px; line-height: 32px;\"><strong>Featured Article</strong></span></p><p style=\"font-size: 14px; line-height: 160%;\"><strong>10 Tips to Boost Your Email Marketing in 2025</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Discover proven strategies that top marketers use to increase open rates, click-throughs, and conversions.</p>"},hasDeprecatedFontControls:true},{id:"nb1",type:"button",values:{containerPadding:"5px 40px 20px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#FFFFFF",backgroundColor:"#f5576c",hoverColor:"#FFFFFF",hoverBackgroundColor:"#fa709a"},size:{autoWidth:true,width:"100%"},fontSize:"14px",textAlign:"left",lineHeight:"120%",padding:"10px 20px",border:{},borderRadius:"4px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Read More</strong>",calculatedWidth:135,calculatedHeight:36},hasDeprecatedFontControls:true},{id:"nd1",type:"divider",values:{width:"100%",border:{borderTopWidth:"1px",borderTopStyle:"solid",borderTopColor:"#eeeeee"},textAlign:"center",containerPadding:"10px 40px",anchor:"",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_divider_1",htmlClassNames:"u_content_divider"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false}}],values:{_meta:{htmlID:"u_column_2",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_2",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"nr3",cells:[1,1],columns:[{id:"nc3",contents:[{id:"nt3",type:"text",values:{containerPadding:"15px 20px 15px 40px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><strong>Industry Trends</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Latest market analysis and what it means for your business.</p><p style=\"font-size: 14px; line-height: 160%;\"><a href=\"#\">Read more </a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}},{id:"nc4",contents:[{id:"nt4",type:"text",values:{containerPadding:"15px 40px 15px 20px",anchor:"",fontSize:"14px",textAlign:"left",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#f5576c",linkHoverColor:"#f5576c",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_4",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\"><strong>Quick Tips</strong></p><p style=\"font-size: 14px; line-height: 160%;\">Simple actionable advice you can implement today.</p><p style=\"font-size: 14px; line-height: 160%;\"><a href=\"#\">Read more </a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_4",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#ffffff",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},
 {id:"nr5",cells:[1],columns:[{id:"nc6",contents:[{id:"nt7",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_7",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company | <a href=\"@{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_6",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_5",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}
 ],
 headers:[],footers:[],
 values:{popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#333333",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#e8e8e8",preheaderText:"Your weekly newsletter",linkStyle:{body:true,linkColor:"#f5576c",linkHoverColor:"#fa709a",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}
 },
 schemaVersion:16
 },

 product_launch: JSON.parse(JSON.stringify({counters:{u_row:3,u_column:3,u_content_text:3,u_content_button:1},body:{id:"pl_body",rows:[{id:"pr1",cells:[1],columns:[{id:"pc1",contents:[{id:"pt1",type:"text",values:{containerPadding:"50px 40px 10px",anchor:"",fontSize:"14px",color:"#ffffff",textAlign:"center",lineHeight:"140%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_1",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>Introducing Our<br>Latest Innovation</strong></span></p>"},hasDeprecatedFontControls:true},{id:"pt2",type:"text",values:{containerPadding:"10px 40px 20px",anchor:"",fontSize:"14px",color:"#d4e5ff",textAlign:"center",lineHeight:"160%",linkStyle:{inherit:true,linkColor:"#ffffff",linkHoverColor:"#ffffff",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_2",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 14px; line-height: 160%;\">Experience the future of productivity with our brand new product.</p>"},hasDeprecatedFontControls:true},{id:"pb1",type:"button",values:{containerPadding:"10px 40px 50px",anchor:"",href:{name:"web",values:{href:"",target:"_blank"}},buttonColors:{color:"#4facfe",backgroundColor:"#ffffff",hoverColor:"#FFFFFF",hoverBackgroundColor:"#4facfe"},size:{autoWidth:true,width:"100%"},fontSize:"16px",textAlign:"center",lineHeight:"120%",padding:"14px 30px",border:{},borderRadius:"30px",hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_button_1",htmlClassNames:"u_content_button"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<strong>Learn More</strong>",calculatedWidth:152,calculatedHeight:47},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_1",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_1",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}},{id:"pr3",cells:[1],columns:[{id:"pc3",contents:[{id:"pt3",type:"text",values:{containerPadding:"20px 40px",anchor:"",fontSize:"12px",color:"#999999",textAlign:"center",lineHeight:"150%",linkStyle:{inherit:true,linkColor:"#999999",linkHoverColor:"#999999",linkUnderline:true,linkHoverUnderline:true},hideDesktop:false,displayCondition:null,_meta:{htmlID:"u_content_text_3",htmlClassNames:"u_content_text"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,text:"<p style=\"font-size: 12px; line-height: 150%;\">© 2025 Your Company | <a href=\"@{{unsubscribe_url}}\">Unsubscribe</a></p>"},hasDeprecatedFontControls:true}],values:{_meta:{htmlID:"u_column_3",htmlClassNames:"u_column"},border:{},padding:"0px",backgroundColor:""}}],values:{displayCondition:null,columns:false,backgroundColor:"",columnsBackgroundColor:"#f5f5f5",backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},padding:"0px",anchor:"",hideDesktop:false,_meta:{htmlID:"u_row_3",htmlClassNames:"u_row"},selectable:true,draggable:true,duplicatable:true,deletable:true,hideable:true,hideMobile:false,noStackMobile:false}}],headers:[],footers:[],values:{popupPosition:"center",popupWidth:"600px",popupHeight:"auto",borderRadius:"10px",contentAlign:"center",contentVerticalAlign:"center",contentWidth:"600px",fontFamily:{label:"Arial",value:"arial,helvetica,sans-serif"},textColor:"#333333",popupBackgroundColor:"#FFFFFF",popupBackgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"cover",position:"center"},popupOverlay_backgroundColor:"rgba(0, 0, 0, 0.1)",popupCloseButton_position:"top-right",popupCloseButton_backgroundColor:"#DDDDDD",popupCloseButton_iconColor:"#000000",popupCloseButton_borderRadius:"0px",popupCloseButton_margin:"0px",popupCloseButton_action:{name:"close_popup",attrs:{onClick:"document.querySelector('.u-popup-container').style.display = 'none';"}},backgroundColor:"#4facfe",preheaderText:"Check out our new product!",linkStyle:{body:true,linkColor:"#4facfe",linkHoverColor:"#00f2fe",linkUnderline:true,linkHoverUnderline:true},backgroundImage:{url:"",fullWidth:true,repeat:"no-repeat",size:"custom",position:"top-center"},_meta:{htmlID:"u_body",htmlClassNames:"u_body"}}},schemaVersion:16}))
};

// Clone welcome as base for other templates
[
 'sale_promotion','event_invitation','thank_you','re_engagement','recruitment','holiday_greeting','simple_text','announcement',
 // Healthcare
 'healthcare_appointment','healthcare_tips','healthcare_welcome',
 // Education
 'education_enrollment','education_course','education_webinar',
 // Accounting
 'accounting_invoice','accounting_report','accounting_tax',
 // Real Estate
 'realestate_listing','realestate_openhouse',
 // Restaurant
 'restaurant_menu','restaurant_reservation',
 // Fitness
 'fitness_membership','fitness_workout',
 // Technology
 'tech_update','tech_onboarding','tech_security',
 // Beauty
 'beauty_spa','beauty_collection',
 // Legal
 'legal_newsletter','legal_client',
 // Travel
 'travel_destination','travel_booking',
 // Nonprofit
 'nonprofit_donation','nonprofit_volunteer',
 // E-Commerce
 'ecommerce_order','ecommerce_cart','ecommerce_review',
 // More Recruitment
 'recruitment_interview','recruitment_offer',
 // More Marketing
 'marketing_referral','marketing_survey','marketing_loyalty',
 // Events
 'birthday_greeting','password_reset'
].forEach(function(key) {
 if (!exampleTemplates[key]) {
 exampleTemplates[key] = JSON.parse(JSON.stringify(exampleTemplates.welcome));
 }
});

// Customize Sale Promotion
exampleTemplates.sale_promotion.body.rows[0].values.columnsBackgroundColor = "#fa709a";
exampleTemplates.sale_promotion.body.rows[1].values.columnsBackgroundColor = "#fa709a";
exampleTemplates.sale_promotion.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>FLASH SALE</strong></span></p><p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 20px; line-height: 28px;\">Up to 50% OFF Everything!</span></p>";
exampleTemplates.sale_promotion.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Don't miss out on our biggest sale of the year!</p>";
exampleTemplates.sale_promotion.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#fa709a",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f5576c"};
exampleTemplates.sale_promotion.body.rows[2].columns[0].contents[1].values.text = "<strong>Shop Now</strong>";
exampleTemplates.sale_promotion.body.values.backgroundColor = "#fee140";

// Customize Event Invitation
exampleTemplates.event_invitation.body.rows[0].values.columnsBackgroundColor = "#a18cd1";
exampleTemplates.event_invitation.body.rows[1].values.columnsBackgroundColor = "#a18cd1";
exampleTemplates.event_invitation.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>You're Invited!</strong></span></p>";
exampleTemplates.event_invitation.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#a18cd1",hoverColor:"#FFFFFF",hoverBackgroundColor:"#764ba2"};
exampleTemplates.event_invitation.body.rows[2].columns[0].contents[1].values.text = "<strong>RSVP Now</strong>";

// Customize Thank You
exampleTemplates.thank_you.body.rows[0].values.columnsBackgroundColor = "#43e97b";
exampleTemplates.thank_you.body.rows[1].values.columnsBackgroundColor = "#43e97b";
exampleTemplates.thank_you.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Thank You!</strong></span></p>";
exampleTemplates.thank_you.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#43e97b",hoverColor:"#FFFFFF",hoverBackgroundColor:"#38f9d7"};
exampleTemplates.thank_you.body.rows[2].columns[0].contents[1].values.text = "<strong>Track Order</strong>";

// Customize Re-engagement
exampleTemplates.re_engagement.body.rows[0].values.columnsBackgroundColor = "#f6d365";
exampleTemplates.re_engagement.body.rows[1].values.columnsBackgroundColor = "#f6d365";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>We Miss You!</strong></span></p>";
exampleTemplates.re_engagement.body.rows[1].columns[0].contents[0].values.color = "#333333";
exampleTemplates.re_engagement.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#fda085",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f6d365"};
exampleTemplates.re_engagement.body.rows[2].columns[0].contents[1].values.text = "<strong>Come Back & Save 20%</strong>";

// Customize Recruitment
exampleTemplates.recruitment.body.rows[0].values.columnsBackgroundColor = "#0c3483";
exampleTemplates.recruitment.body.rows[1].values.columnsBackgroundColor = "#0c3483";
exampleTemplates.recruitment.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>We're Hiring!</strong></span></p>";
exampleTemplates.recruitment.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#0c3483",hoverColor:"#FFFFFF",hoverBackgroundColor:"#a2b6df"};
exampleTemplates.recruitment.body.rows[2].columns[0].contents[1].values.text = "<strong>View Open Positions</strong>";

// Customize Holiday
exampleTemplates.holiday_greeting.body.rows[0].values.columnsBackgroundColor = "#ff0844";
exampleTemplates.holiday_greeting.body.rows[1].values.columnsBackgroundColor = "#ff0844";
exampleTemplates.holiday_greeting.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>Happy Holidays!</strong></span></p>";
exampleTemplates.holiday_greeting.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#ff0844",hoverColor:"#FFFFFF",hoverBackgroundColor:"#ffb199"};
exampleTemplates.holiday_greeting.body.rows[2].columns[0].contents[1].values.text = "<strong>Claim Your Gift</strong>";
exampleTemplates.holiday_greeting.body.values.backgroundColor = "#ffb199";

// Customize Simple Text
exampleTemplates.simple_text.body.rows[0].values.columnsBackgroundColor = "#2c3e50";
exampleTemplates.simple_text.body.rows[1].values.columnsBackgroundColor = "#2c3e50";
exampleTemplates.simple_text.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 24px; line-height: 33.6px;\"><strong>Your Company Name</strong></span></p>";
exampleTemplates.simple_text.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2c3e50",hoverColor:"#FFFFFF",hoverBackgroundColor:"#4ca1af"};
exampleTemplates.simple_text.body.rows[2].columns[0].contents[1].values.text = "<strong>Visit Our Website</strong>";

// Customize Announcement
exampleTemplates.announcement.body.rows[0].values.columnsBackgroundColor = "#292E49";
exampleTemplates.announcement.body.rows[1].values.columnsBackgroundColor = "#292E49";
exampleTemplates.announcement.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Important Announcement</strong></span></p>";
exampleTemplates.announcement.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#292E49",hoverColor:"#FFFFFF",hoverBackgroundColor:"#536976"};
exampleTemplates.announcement.body.rows[2].columns[0].contents[1].values.text = "<strong>Learn More</strong>";

// =============================================
// HEALTHCARE TEMPLATES
// =============================================

// Healthcare - Appointment Reminder
exampleTemplates.healthcare_appointment.body.rows[0].values.columnsBackgroundColor = "#00b09b";
exampleTemplates.healthcare_appointment.body.rows[1].values.columnsBackgroundColor = "#00b09b";
exampleTemplates.healthcare_appointment.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Appointment Reminder</strong></span></p>";
exampleTemplates.healthcare_appointment.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Your upcoming appointment is confirmed. Please arrive 15 minutes early.</p>";
exampleTemplates.healthcare_appointment.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Patient,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Appointment Details:</strong></p><p style=\"font-size: 14px; line-height: 170%;\"> Date: [Date]<br>Time: [Time]<br>Doctor: Dr. [Name]<br>Location: [Clinic Address]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Please bring your insurance card and any relevant medical records.</p>";
exampleTemplates.healthcare_appointment.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#00b09b",hoverColor:"#FFFFFF",hoverBackgroundColor:"#96c93d"};
exampleTemplates.healthcare_appointment.body.rows[2].columns[0].contents[1].values.text = "<strong>Confirm Appointment</strong>";
exampleTemplates.healthcare_appointment.body.values.backgroundColor = "#e8f5e9";

// Healthcare - Health Tips
exampleTemplates.healthcare_tips.body.rows[0].values.columnsBackgroundColor = "#11998e";
exampleTemplates.healthcare_tips.body.rows[1].values.columnsBackgroundColor = "#11998e";
exampleTemplates.healthcare_tips.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Monthly Health Tips</strong></span></p>";
exampleTemplates.healthcare_tips.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Your guide to a healthier lifestyle this month</p>";
exampleTemplates.healthcare_tips.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"> <strong>Nutrition:</strong> Include more leafy greens in your diet</p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Exercise:</strong> 30 minutes of daily walking boosts heart health</p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Sleep:</strong> Aim for 7-8 hours of quality sleep</p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Mental Health:</strong> Practice 10 minutes of meditation daily</p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Hydration:</strong> Drink at least 8 glasses of water daily</p>";
exampleTemplates.healthcare_tips.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#11998e",hoverColor:"#FFFFFF",hoverBackgroundColor:"#38ef7d"};
exampleTemplates.healthcare_tips.body.rows[2].columns[0].contents[1].values.text = "<strong>Read More Health Tips</strong>";

// Healthcare - Patient Welcome
exampleTemplates.healthcare_welcome.body.rows[0].values.columnsBackgroundColor = "#2193b0";
exampleTemplates.healthcare_welcome.body.rows[1].values.columnsBackgroundColor = "#2193b0";
exampleTemplates.healthcare_welcome.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Welcome to Our Clinic</strong></span></p>";
exampleTemplates.healthcare_welcome.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">We're dedicated to providing you with the best healthcare experience</p>";
exampleTemplates.healthcare_welcome.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Patient,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Welcome! We're glad you've chosen us for your healthcare needs. Here's what you need to know:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Complete your patient registration online<br>Upload your insurance information<br>Review our service offerings<br>Book your first appointment</p>";
exampleTemplates.healthcare_welcome.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2193b0",hoverColor:"#FFFFFF",hoverBackgroundColor:"#6dd5ed"};
exampleTemplates.healthcare_welcome.body.rows[2].columns[0].contents[1].values.text = "<strong>Complete Registration</strong>";

// =============================================
// EDUCATION TEMPLATES
// =============================================

// Education - Course Enrollment
exampleTemplates.education_enrollment.body.rows[0].values.columnsBackgroundColor = "#4568DC";
exampleTemplates.education_enrollment.body.rows[1].values.columnsBackgroundColor = "#4568DC";
exampleTemplates.education_enrollment.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Enrollment Confirmed!</strong></span></p>";
exampleTemplates.education_enrollment.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Welcome to the beginning of your learning journey</p>";
exampleTemplates.education_enrollment.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Congratulations on enrolling! Here's your course details:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Course:</strong> [Course Name]<br><strong>Start Date:</strong> [Date]<br>⏰ <strong>Duration:</strong> [Duration]<br><strong>Instructor:</strong> [Name]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Access your course materials and get started right away!</p>";
exampleTemplates.education_enrollment.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#4568DC",hoverColor:"#FFFFFF",hoverBackgroundColor:"#B06AB3"};
exampleTemplates.education_enrollment.body.rows[2].columns[0].contents[1].values.text = "<strong>Access Course Materials</strong>";

// Education - Online Course
exampleTemplates.education_course.body.rows[0].values.columnsBackgroundColor = "#FF512F";
exampleTemplates.education_course.body.rows[1].values.columnsBackgroundColor = "#FF512F";
exampleTemplates.education_course.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>New Course Available!</strong></span></p>";
exampleTemplates.education_course.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Master new skills with our latest online course</p>";
exampleTemplates.education_course.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">We've just launched a brand new course designed to help you advance your career:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Industry-relevant curriculum<br>HD video lessons<br>Hands-on projects<br>Certificate upon completion<br>Community support</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Early bird discount: 30% OFF for the first 100 students!</strong></p>";
exampleTemplates.education_course.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#FF512F",hoverColor:"#FFFFFF",hoverBackgroundColor:"#F09819"};
exampleTemplates.education_course.body.rows[2].columns[0].contents[1].values.text = "<strong>Enroll Now</strong>";
exampleTemplates.education_course.body.values.backgroundColor = "#fff3e0";

// Education - Webinar
exampleTemplates.education_webinar.body.rows[0].values.columnsBackgroundColor = "#1A2980";
exampleTemplates.education_webinar.body.rows[1].values.columnsBackgroundColor = "#1A2980";
exampleTemplates.education_webinar.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Free Webinar Invitation</strong></span></p>";
exampleTemplates.education_webinar.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Join industry experts for a live learning session</p>";
exampleTemplates.education_webinar.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"><strong>Topic:</strong> [Webinar Title]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Date: [Date]<br>Time: [Time] (GMT+7)<br>⏱ Duration: 60 minutes<br>Free materials included</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Limited spots available - Register now to secure your seat!</p>";
exampleTemplates.education_webinar.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#1A2980",hoverColor:"#FFFFFF",hoverBackgroundColor:"#26D0CE"};
exampleTemplates.education_webinar.body.rows[2].columns[0].contents[1].values.text = "<strong>Register for Free</strong>";

// =============================================
// ACCOUNTING / FINANCE TEMPLATES
// =============================================

// Accounting - Invoice
exampleTemplates.accounting_invoice.body.rows[0].values.columnsBackgroundColor = "#2C3E50";
exampleTemplates.accounting_invoice.body.rows[1].values.columnsBackgroundColor = "#2C3E50";
exampleTemplates.accounting_invoice.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Invoice Reminder</strong></span></p>";
exampleTemplates.accounting_invoice.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Payment due - please review your outstanding invoice</p>";
exampleTemplates.accounting_invoice.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Client,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">This is a friendly reminder that your invoice is due:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Invoice #:</strong> [Number]<br><strong>Amount:</strong> $[Amount]<br><strong>Due Date:</strong> [Date]<br><strong>Payment Method:</strong> Bank transfer / Online</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Please process the payment at your earliest convenience.</p>";
exampleTemplates.accounting_invoice.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#3498DB",hoverColor:"#FFFFFF",hoverBackgroundColor:"#2C3E50"};
exampleTemplates.accounting_invoice.body.rows[2].columns[0].contents[1].values.text = "<strong>Pay Invoice</strong>";

// Accounting - Financial Report
exampleTemplates.accounting_report.body.rows[0].values.columnsBackgroundColor = "#134E5E";
exampleTemplates.accounting_report.body.rows[1].values.columnsBackgroundColor = "#134E5E";
exampleTemplates.accounting_report.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Financial Report</strong></span></p>";
exampleTemplates.accounting_report.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Your monthly financial summary is ready</p>";
exampleTemplates.accounting_report.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Client,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Your [Month] financial report is now available:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Revenue: $[Amount]<br>Expenses: $[Amount]<br>Net Profit: $[Amount]<br>Tax Obligations: $[Amount]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Download the full report for detailed breakdown and insights.</p>";
exampleTemplates.accounting_report.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#134E5E",hoverColor:"#FFFFFF",hoverBackgroundColor:"#71B280"};
exampleTemplates.accounting_report.body.rows[2].columns[0].contents[1].values.text = "<strong>Download Report</strong>";

// Accounting - Tax Season
exampleTemplates.accounting_tax.body.rows[0].values.columnsBackgroundColor = "#1D4350";
exampleTemplates.accounting_tax.body.rows[1].values.columnsBackgroundColor = "#1D4350";
exampleTemplates.accounting_tax.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Tax Season Reminder</strong></span></p>";
exampleTemplates.accounting_tax.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Important tax deadlines are approaching</p>";
exampleTemplates.accounting_tax.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Client,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Tax filing deadlines are approaching. Here's what you need to prepare:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Gather all income documents<br>Organize expense receipts<br>Review deduction eligibility<br>File before [Deadline Date]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Need help? Our tax experts are ready to assist you.</p>";
exampleTemplates.accounting_tax.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#A43931",hoverColor:"#FFFFFF",hoverBackgroundColor:"#1D4350"};
exampleTemplates.accounting_tax.body.rows[2].columns[0].contents[1].values.text = "<strong>Schedule Consultation</strong>";

// =============================================
// REAL ESTATE TEMPLATES
// =============================================

exampleTemplates.realestate_listing.body.rows[0].values.columnsBackgroundColor = "#C33764";
exampleTemplates.realestate_listing.body.rows[1].values.columnsBackgroundColor = "#C33764";
exampleTemplates.realestate_listing.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>New Property Listing</strong></span></p>";
exampleTemplates.realestate_listing.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">A stunning property just hit the market in your area</p>";
exampleTemplates.realestate_listing.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"><strong>Property Highlights:</strong></p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> [Property Type] - [Bedrooms] BR / [Bathrooms] BA<br>Location: [Address]<br>Area: [Size] sqft<br>Price: $[Price]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> Modern kitchen Spacious backyard 2-car garage Close to schools</p>";
exampleTemplates.realestate_listing.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#C33764",hoverColor:"#FFFFFF",hoverBackgroundColor:"#1D2671"};
exampleTemplates.realestate_listing.body.rows[2].columns[0].contents[1].values.text = "<strong>View Property Details</strong>";

exampleTemplates.realestate_openhouse.body.rows[0].values.columnsBackgroundColor = "#E44D26";
exampleTemplates.realestate_openhouse.body.rows[1].values.columnsBackgroundColor = "#E44D26";
exampleTemplates.realestate_openhouse.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Open House This Weekend</strong></span></p>";
exampleTemplates.realestate_openhouse.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#E44D26",hoverColor:"#FFFFFF",hoverBackgroundColor:"#F16529"};
exampleTemplates.realestate_openhouse.body.rows[2].columns[0].contents[1].values.text = "<strong>RSVP Now</strong>";

// =============================================
// RESTAURANT TEMPLATES
// =============================================

exampleTemplates.restaurant_menu.body.rows[0].values.columnsBackgroundColor = "#D4145A";
exampleTemplates.restaurant_menu.body.rows[1].values.columnsBackgroundColor = "#D4145A";
exampleTemplates.restaurant_menu.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>New Menu is Here!</strong></span></p>";
exampleTemplates.restaurant_menu.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Fresh flavors, new dishes, same great experience</p>";
exampleTemplates.restaurant_menu.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">We've refreshed our menu with exciting new dishes:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Starters:</strong> Truffle Mushroom Soup, Caesar Salad<br><strong>Mains:</strong> Grilled Wagyu Steak, Pan-Seared Salmon<br><strong>Desserts:</strong> Tiramisu, Chocolate Lava Cake<br><strong>Drinks:</strong> Craft cocktails & fine wines</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Show this email for 15% off your first order!</strong></p>";
exampleTemplates.restaurant_menu.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#D4145A",hoverColor:"#FFFFFF",hoverBackgroundColor:"#FBB03B"};
exampleTemplates.restaurant_menu.body.rows[2].columns[0].contents[1].values.text = "<strong>View Full Menu</strong>";
exampleTemplates.restaurant_menu.body.values.backgroundColor = "#fff8e1";

exampleTemplates.restaurant_reservation.body.rows[0].values.columnsBackgroundColor = "#6B0F1A";
exampleTemplates.restaurant_reservation.body.rows[1].values.columnsBackgroundColor = "#6B0F1A";
exampleTemplates.restaurant_reservation.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Reserve Your Table</strong></span></p>";
exampleTemplates.restaurant_reservation.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#6B0F1A",hoverColor:"#FFFFFF",hoverBackgroundColor:"#B91D73"};
exampleTemplates.restaurant_reservation.body.rows[2].columns[0].contents[1].values.text = "<strong>Book Now</strong>";

// =============================================
// FITNESS TEMPLATES
// =============================================

exampleTemplates.fitness_membership.body.rows[0].values.columnsBackgroundColor = "#f12711";
exampleTemplates.fitness_membership.body.rows[1].values.columnsBackgroundColor = "#f12711";
exampleTemplates.fitness_membership.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Join Our Gym Today!</strong></span></p>";
exampleTemplates.fitness_membership.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Transform your body, transform your life</p>";
exampleTemplates.fitness_membership.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Start your fitness journey with us:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> State-of-the-art equipment<br>Group fitness classes (Yoga, HIIT, Zumba)<br>Personal training sessions<br>Premium locker rooms & sauna<br>Free fitness tracking app</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Limited offer: First month FREE + 20% off annual plan!</strong></p>";
exampleTemplates.fitness_membership.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#f12711",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f5af19"};
exampleTemplates.fitness_membership.body.rows[2].columns[0].contents[1].values.text = "<strong>Claim Free Trial</strong>";

exampleTemplates.fitness_workout.body.rows[0].values.columnsBackgroundColor = "#0F2027";
exampleTemplates.fitness_workout.body.rows[1].values.columnsBackgroundColor = "#0F2027";
exampleTemplates.fitness_workout.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Your Weekly Workout Plan</strong></span></p>";
exampleTemplates.fitness_workout.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2C5364",hoverColor:"#FFFFFF",hoverBackgroundColor:"#0F2027"};
exampleTemplates.fitness_workout.body.rows[2].columns[0].contents[1].values.text = "<strong>View Full Plan</strong>";

// =============================================
// TECHNOLOGY TEMPLATES
// =============================================

exampleTemplates.tech_update.body.rows[0].values.columnsBackgroundColor = "#302b63";
exampleTemplates.tech_update.body.rows[1].values.columnsBackgroundColor = "#302b63";
exampleTemplates.tech_update.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Product Update v2.0</strong></span></p>";
exampleTemplates.tech_update.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Exciting new features and improvements</p>";
exampleTemplates.tech_update.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">What's new in this release:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Performance:</strong> 3x faster loading speeds<br><strong>UI:</strong> Redesigned dashboard<br><strong>Tools:</strong> Advanced analytics module<br><strong>Security:</strong> Two-factor authentication<br><strong>Mobile:</strong> Native iOS & Android apps</p>";
exampleTemplates.tech_update.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#302b63",hoverColor:"#FFFFFF",hoverBackgroundColor:"#24243e"};
exampleTemplates.tech_update.body.rows[2].columns[0].contents[1].values.text = "<strong>See What's New</strong>";

exampleTemplates.tech_onboarding.body.rows[0].values.columnsBackgroundColor = "#0072ff";
exampleTemplates.tech_onboarding.body.rows[1].values.columnsBackgroundColor = "#0072ff";
exampleTemplates.tech_onboarding.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Welcome to [Product]!</strong></span></p>";
exampleTemplates.tech_onboarding.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Let's get you started in 3 easy steps</p>";
exampleTemplates.tech_onboarding.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"><strong>Step 1:</strong> Complete your profile setup<br><strong>Step 2:</strong> Connect your first integration<br><strong>Step 3:</strong> Invite your team members</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Need help? Check our docs or chat with support 24/7.</p>";
exampleTemplates.tech_onboarding.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#0072ff",hoverColor:"#FFFFFF",hoverBackgroundColor:"#00c6ff"};
exampleTemplates.tech_onboarding.body.rows[2].columns[0].contents[1].values.text = "<strong>Complete Setup</strong>";

exampleTemplates.tech_security.body.rows[0].values.columnsBackgroundColor = "#dd1818";
exampleTemplates.tech_security.body.rows[1].values.columnsBackgroundColor = "#dd1818";
exampleTemplates.tech_security.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Security Alert</strong></span></p>";
exampleTemplates.tech_security.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#dd1818",hoverColor:"#FFFFFF",hoverBackgroundColor:"#333333"};
exampleTemplates.tech_security.body.rows[2].columns[0].contents[1].values.text = "<strong>Review Activity</strong>";

// =============================================
// BEAUTY / SPA TEMPLATES
// =============================================

exampleTemplates.beauty_spa.body.rows[0].values.columnsBackgroundColor = "#636fa4";
exampleTemplates.beauty_spa.body.rows[1].values.columnsBackgroundColor = "#636fa4";
exampleTemplates.beauty_spa.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Spa & Beauty Special</strong></span></p>";
exampleTemplates.beauty_spa.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Treat yourself to the ultimate relaxation experience</p>";
exampleTemplates.beauty_spa.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">This month's special packages:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Deep Tissue Massage</strong> - 60 min - $79<br><strong>Facial Treatment</strong> - 45 min - $59<br><strong>Manicure & Pedicure</strong> - 90 min - $49<br><strong>Full Body Spa Package</strong> - 120 min - $149</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Book 2 services, get 20% OFF!</strong></p>";
exampleTemplates.beauty_spa.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#636fa4",hoverColor:"#FFFFFF",hoverBackgroundColor:"#e8cbc0"};
exampleTemplates.beauty_spa.body.rows[2].columns[0].contents[1].values.text = "<strong>Book Appointment</strong>";
exampleTemplates.beauty_spa.body.values.backgroundColor = "#f3e5f5";

exampleTemplates.beauty_collection.body.rows[0].values.columnsBackgroundColor = "#ee9ca7";
exampleTemplates.beauty_collection.body.rows[1].values.columnsBackgroundColor = "#ee9ca7";
exampleTemplates.beauty_collection.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>New Beauty Collection</strong></span></p>";
exampleTemplates.beauty_collection.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#ee9ca7",hoverColor:"#FFFFFF",hoverBackgroundColor:"#ffdde1"};
exampleTemplates.beauty_collection.body.rows[2].columns[0].contents[1].values.text = "<strong>Shop Now</strong>";

// =============================================
// LEGAL TEMPLATES
// =============================================

exampleTemplates.legal_newsletter.body.rows[0].values.columnsBackgroundColor = "#1c1c1c";
exampleTemplates.legal_newsletter.body.rows[1].values.columnsBackgroundColor = "#1c1c1c";
exampleTemplates.legal_newsletter.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Legal Update</strong></span></p>";
exampleTemplates.legal_newsletter.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Important legal news and regulatory changes</p>";
exampleTemplates.legal_newsletter.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">This month's key legal developments:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> New labor law amendments effective [Date]<br>Updated data privacy regulations<br>Tax compliance checklist for businesses<br>Recent court decisions affecting SMEs</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Our legal team is available for consultations.</p>";
exampleTemplates.legal_newsletter.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#1c1c1c",hoverColor:"#FFFFFF",hoverBackgroundColor:"#434343"};
exampleTemplates.legal_newsletter.body.rows[2].columns[0].contents[1].values.text = "<strong>Read Full Update</strong>";

exampleTemplates.legal_client.body.rows[0].values.columnsBackgroundColor = "#373B44";
exampleTemplates.legal_client.body.rows[1].values.columnsBackgroundColor = "#373B44";
exampleTemplates.legal_client.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Case Status Update</strong></span></p>";
exampleTemplates.legal_client.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#4286f4",hoverColor:"#FFFFFF",hoverBackgroundColor:"#373B44"};
exampleTemplates.legal_client.body.rows[2].columns[0].contents[1].values.text = "<strong>View Case Details</strong>";

// =============================================
// TRAVEL TEMPLATES
// =============================================

exampleTemplates.travel_destination.body.rows[0].values.columnsBackgroundColor = "#1CB5E0";
exampleTemplates.travel_destination.body.rows[1].values.columnsBackgroundColor = "#1CB5E0";
exampleTemplates.travel_destination.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Exclusive Travel Deals</strong></span></p>";
exampleTemplates.travel_destination.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Discover amazing destinations at unbeatable prices</p>";
exampleTemplates.travel_destination.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">This week's top travel deals:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Bali, Indonesia</strong> - from $499/person<br><strong>Paris, France</strong> - from $699/person<br><strong>Tokyo, Japan</strong> - from $799/person<br><strong>Maldives</strong> - from $999/person</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Book before [Date] and save an extra 10%!</strong></p>";
exampleTemplates.travel_destination.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#1CB5E0",hoverColor:"#FFFFFF",hoverBackgroundColor:"#000851"};
exampleTemplates.travel_destination.body.rows[2].columns[0].contents[1].values.text = "<strong>Explore Deals</strong>";
exampleTemplates.travel_destination.body.values.backgroundColor = "#e0f7fa";

exampleTemplates.travel_booking.body.rows[0].values.columnsBackgroundColor = "#56ab2f";
exampleTemplates.travel_booking.body.rows[1].values.columnsBackgroundColor = "#56ab2f";
exampleTemplates.travel_booking.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Booking Confirmed!</strong></span></p>";
exampleTemplates.travel_booking.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#56ab2f",hoverColor:"#FFFFFF",hoverBackgroundColor:"#a8e063"};
exampleTemplates.travel_booking.body.rows[2].columns[0].contents[1].values.text = "<strong>View Booking Details</strong>";

// =============================================
// NONPROFIT TEMPLATES
// =============================================

exampleTemplates.nonprofit_donation.body.rows[0].values.columnsBackgroundColor = "#f7971e";
exampleTemplates.nonprofit_donation.body.rows[1].values.columnsBackgroundColor = "#f7971e";
exampleTemplates.nonprofit_donation.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Make a Difference Today</strong></span></p>";
exampleTemplates.nonprofit_donation.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Your donation can change lives</p>";
exampleTemplates.nonprofit_donation.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Supporter,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">With your help, we've achieved:</p><p style=\"font-size: 14px; line-height: 170%;\"> Built 12 schools in rural areas<br>Provided 50,000 meals to families<br>Distributed 10,000 textbooks<br>Medical aid to 5,000 patients</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Every dollar counts. Help us continue this mission.</p>";
exampleTemplates.nonprofit_donation.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#f7971e",hoverColor:"#FFFFFF",hoverBackgroundColor:"#ffd200"};
exampleTemplates.nonprofit_donation.body.rows[2].columns[0].contents[1].values.text = "<strong>Donate Now</strong>";
exampleTemplates.nonprofit_donation.body.values.backgroundColor = "#fffde7";

exampleTemplates.nonprofit_volunteer.body.rows[0].values.columnsBackgroundColor = "#11998e";
exampleTemplates.nonprofit_volunteer.body.rows[1].values.columnsBackgroundColor = "#11998e";
exampleTemplates.nonprofit_volunteer.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Volunteer With Us</strong></span></p>";
exampleTemplates.nonprofit_volunteer.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#11998e",hoverColor:"#FFFFFF",hoverBackgroundColor:"#38ef7d"};
exampleTemplates.nonprofit_volunteer.body.rows[2].columns[0].contents[1].values.text = "<strong>Sign Up to Volunteer</strong>";

// =============================================
// E-COMMERCE TEMPLATES
// =============================================

exampleTemplates.ecommerce_order.body.rows[0].values.columnsBackgroundColor = "#d76d77";
exampleTemplates.ecommerce_order.body.rows[1].values.columnsBackgroundColor = "#d76d77";
exampleTemplates.ecommerce_order.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Order Confirmed!</strong></span></p>";
exampleTemplates.ecommerce_order.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Thank you for your purchase</p>";
exampleTemplates.ecommerce_order.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Your order has been confirmed:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Order #:</strong> [Number]<br><strong>Date:</strong> [Date]<br><strong>Total:</strong> $[Amount]<br><strong>Estimated Delivery:</strong> [Date]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">We'll send you a tracking number once your order ships.</p>";
exampleTemplates.ecommerce_order.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#d76d77",hoverColor:"#FFFFFF",hoverBackgroundColor:"#3a1c71"};
exampleTemplates.ecommerce_order.body.rows[2].columns[0].contents[1].values.text = "<strong>Track Order</strong>";

exampleTemplates.ecommerce_cart.body.rows[0].values.columnsBackgroundColor = "#e53935";
exampleTemplates.ecommerce_cart.body.rows[1].values.columnsBackgroundColor = "#e53935";
exampleTemplates.ecommerce_cart.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>You Left Something Behind!</strong></span></p>";
exampleTemplates.ecommerce_cart.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Complete your purchase before items sell out</p>";
exampleTemplates.ecommerce_cart.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">You left items in your cart. Don't miss out!</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>Use code COMEBACK10 for 10% off your order!</strong></p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">This offer expires in 24 hours.</p>";
exampleTemplates.ecommerce_cart.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#e53935",hoverColor:"#FFFFFF",hoverBackgroundColor:"#e35d5b"};
exampleTemplates.ecommerce_cart.body.rows[2].columns[0].contents[1].values.text = "<strong>Complete Purchase</strong>";

exampleTemplates.ecommerce_review.body.rows[0].values.columnsBackgroundColor = "#f5576c";
exampleTemplates.ecommerce_review.body.rows[1].values.columnsBackgroundColor = "#f5576c";
exampleTemplates.ecommerce_review.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>⭐ How Was Your Purchase?</strong></span></p>";
exampleTemplates.ecommerce_review.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#f5576c",hoverColor:"#FFFFFF",hoverBackgroundColor:"#f093fb"};
exampleTemplates.ecommerce_review.body.rows[2].columns[0].contents[1].values.text = "<strong>Leave a Review</strong>";

// =============================================
// MORE RECRUITMENT TEMPLATES
// =============================================

exampleTemplates.recruitment_interview.body.rows[0].values.columnsBackgroundColor = "#2b5876";
exampleTemplates.recruitment_interview.body.rows[1].values.columnsBackgroundColor = "#2b5876";
exampleTemplates.recruitment_interview.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Interview Invitation</strong></span></p>";
exampleTemplates.recruitment_interview.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">We'd love to meet you!</p>";
exampleTemplates.recruitment_interview.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Dear Candidate,</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">We're impressed with your application and would like to invite you for an interview:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"> <strong>Position:</strong> [Job Title]<br><strong>Date:</strong> [Date]<br><strong>Time:</strong> [Time]<br><strong>Location:</strong> [Address / Online Link]</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Please confirm your attendance by clicking below.</p>";
exampleTemplates.recruitment_interview.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2b5876",hoverColor:"#FFFFFF",hoverBackgroundColor:"#4e4376"};
exampleTemplates.recruitment_interview.body.rows[2].columns[0].contents[1].values.text = "<strong>Confirm Attendance</strong>";

exampleTemplates.recruitment_offer.body.rows[0].values.columnsBackgroundColor = "#2980B9";
exampleTemplates.recruitment_offer.body.rows[1].values.columnsBackgroundColor = "#2980B9";
exampleTemplates.recruitment_offer.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Congratulations!</strong></span></p>";
exampleTemplates.recruitment_offer.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">We're excited to offer you a position</p>";
exampleTemplates.recruitment_offer.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#2980B9",hoverColor:"#FFFFFF",hoverBackgroundColor:"#6DD5FA"};
exampleTemplates.recruitment_offer.body.rows[2].columns[0].contents[1].values.text = "<strong>View Offer Letter</strong>";

// =============================================
// MORE MARKETING TEMPLATES
// =============================================

exampleTemplates.marketing_referral.body.rows[0].values.columnsBackgroundColor = "#F7971E";
exampleTemplates.marketing_referral.body.rows[1].values.columnsBackgroundColor = "#F7971E";
exampleTemplates.marketing_referral.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Refer a Friend, Get Rewarded!</strong></span></p>";
exampleTemplates.marketing_referral.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Share the love, earn amazing rewards</p>";
exampleTemplates.marketing_referral.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">How it works:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">1 Share your unique referral link<br>2 Friend signs up using your link<br>3 You both get $25 credit!</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>No limit on referrals - the more you share, the more you earn!</strong></p>";
exampleTemplates.marketing_referral.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#F7971E",hoverColor:"#FFFFFF",hoverBackgroundColor:"#FFD200"};
exampleTemplates.marketing_referral.body.rows[2].columns[0].contents[1].values.text = "<strong>Get My Referral Link</strong>";

exampleTemplates.marketing_survey.body.rows[0].values.columnsBackgroundColor = "#6a3093";
exampleTemplates.marketing_survey.body.rows[1].values.columnsBackgroundColor = "#6a3093";
exampleTemplates.marketing_survey.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>We Value Your Feedback</strong></span></p>";
exampleTemplates.marketing_survey.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#6a3093",hoverColor:"#FFFFFF",hoverBackgroundColor:"#a044ff"};
exampleTemplates.marketing_survey.body.rows[2].columns[0].contents[1].values.text = "<strong>Take the Survey (2 min)</strong>";

exampleTemplates.marketing_loyalty.body.rows[0].values.columnsBackgroundColor = "#C6426E";
exampleTemplates.marketing_loyalty.body.rows[1].values.columnsBackgroundColor = "#C6426E";
exampleTemplates.marketing_loyalty.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>VIP Loyalty Rewards</strong></span></p>";
exampleTemplates.marketing_loyalty.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#C6426E",hoverColor:"#FFFFFF",hoverBackgroundColor:"#642B73"};
exampleTemplates.marketing_loyalty.body.rows[2].columns[0].contents[1].values.text = "<strong>Claim Reward</strong>";

// =============================================
// EVENT & MISC TEMPLATES
// =============================================

exampleTemplates.birthday_greeting.body.rows[0].values.columnsBackgroundColor = "#ee0979";
exampleTemplates.birthday_greeting.body.rows[1].values.columnsBackgroundColor = "#ee0979";
exampleTemplates.birthday_greeting.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 36px; line-height: 50.4px;\"><strong>Happy Birthday!</strong></span></p>";
exampleTemplates.birthday_greeting.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">Wishing you an amazing day!</p>";
exampleTemplates.birthday_greeting.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\"> It's your special day! To celebrate, here's an exclusive birthday gift just for you:</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\"><strong>25% OFF your next purchase!</strong></p><p style=\"font-size: 14px; line-height: 170%;\">Use code: BIRTHDAY25</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">Valid for 7 days. Have a wonderful birthday! </p>";
exampleTemplates.birthday_greeting.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#ee0979",hoverColor:"#FFFFFF",hoverBackgroundColor:"#ff6a00"};
exampleTemplates.birthday_greeting.body.rows[2].columns[0].contents[1].values.text = "<strong>Redeem Birthday Gift</strong>";
exampleTemplates.birthday_greeting.body.values.backgroundColor = "#fce4ec";

exampleTemplates.password_reset.body.rows[0].values.columnsBackgroundColor = "#182848";
exampleTemplates.password_reset.body.rows[1].values.columnsBackgroundColor = "#182848";
exampleTemplates.password_reset.body.rows[1].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 140%;\"><span style=\"font-size: 28px; line-height: 39.2px;\"><strong>Password Reset Request</strong></span></p>";
exampleTemplates.password_reset.body.rows[1].columns[0].contents[1].values.text = "<p style=\"font-size: 14px; line-height: 160%;\">We received a request to reset your password</p>";
exampleTemplates.password_reset.body.rows[2].columns[0].contents[0].values.text = "<p style=\"font-size: 14px; line-height: 170%;\">Click the button below to reset your password. This link will expire in 60 minutes.</p><p style=\"font-size: 14px; line-height: 170%;\"> </p><p style=\"font-size: 14px; line-height: 170%;\">If you didn't request this, please ignore this email. Your password will remain unchanged.</p>";
exampleTemplates.password_reset.body.rows[2].columns[0].contents[1].values.buttonColors = {color:"#FFFFFF",backgroundColor:"#4b6cb7",hoverColor:"#FFFFFF",hoverBackgroundColor:"#182848"};
exampleTemplates.password_reset.body.rows[2].columns[0].contents[1].values.text = "<strong>Reset Password</strong>";

// =============================================
// ADD LOGO ROW TO ALL TEMPLATES (except blank and welcome which already has one)
// =============================================
(function() {
 var logoUrl = "https://placehold.co/140x40.png?text=Your+Logo";

 function makeLogoRow(bgColor, rowId) {
 return {
 id: rowId || "logo_row",
 cells: [1],
 columns: [{
 id: rowId + "_col",
 contents: [{
 id: rowId + "_img",
 type: "image",
 values: {
 containerPadding: "20px 10px 10px",
 anchor: "",
 src: {url: logoUrl, width: 140, height: 40, maxWidth: "25%", autoWidth: false},
 textAlign: "center",
 altText: "Logo",
 action: {name: "web", values: {href: "", target: "_blank"}},
 hideDesktop: false,
 displayCondition: null,
 _meta: {htmlID: "u_content_image_logo", htmlClassNames: "u_content_image"},
 selectable: true, draggable: true, duplicatable: true, deletable: true, hideable: true, hideMobile: false
 }
 }],
 values: {_meta: {htmlID: "u_column_logo", htmlClassNames: "u_column"}, border: {}, padding: "0px", backgroundColor: ""}
 }],
 values: {
 displayCondition: null, columns: false, backgroundColor: "",
 columnsBackgroundColor: bgColor || "#ffffff",
 backgroundImage: {url: "", fullWidth: true, repeat: "no-repeat", size: "custom", position: "top-center"},
 padding: "0px", anchor: "", hideDesktop: false,
 _meta: {htmlID: "u_row_logo", htmlClassNames: "u_row"},
 selectable: true, draggable: true, duplicatable: true, deletable: true, hideable: true, hideMobile: false, noStackMobile: false
 }
 };
 }

 // Map of template keys to their header background color (must match actual JS keys)
 var templateLogos = {
 newsletter: "#f5576c",
 product_launch: "#4facfe",
 sale_promotion: "#fa709a",
 event_invitation: "#a18cd1",
 thank_you: "#43e97b",
 re_engagement: "#f6d365",
 recruitment: "#0c3483",
 holiday_greeting: "#ff0844",
 simple_text: "#2c3e50",
 announcement: "#292E49",
 healthcare_appointment: "#00b09b",
 healthcare_tips: "#11998e",
 healthcare_welcome: "#2193b0",
 education_enrollment: "#4568DC",
 education_course: "#FF512F",
 education_webinar: "#1A2980",
 accounting_invoice: "#2C3E50",
 accounting_report: "#134E5E",
 accounting_tax: "#1D4350",
 realestate_listing: "#C33764",
 realestate_openhouse: "#E44D26",
 restaurant_menu: "#D4145A",
 restaurant_reservation: "#6B0F1A",
 fitness_membership: "#f12711",
 fitness_workout: "#0F2027",
 tech_update: "#302b63",
 tech_onboarding: "#0072ff",
 tech_security: "#dd1818",
 beauty_spa: "#636fa4",
 beauty_collection: "#ee9ca7",
 legal_newsletter: "#1c1c1c",
 legal_client: "#373B44",
 travel_destination: "#1CB5E0",
 travel_booking: "#56ab2f",
 nonprofit_donation: "#f7971e",
 nonprofit_volunteer: "#11998e",
 ecommerce_order: "#d76d77",
 ecommerce_cart: "#e53935",
 ecommerce_review: "#f5576c",
 recruitment_interview: "#2b5876",
 recruitment_offer: "#2980B9",
 marketing_referral: "#F7971E",
 marketing_survey: "#6a3093",
 marketing_loyalty: "#C6426E",
 birthday_greeting: "#ee0979",
 password_reset: "#182848"
 };

 Object.keys(templateLogos).forEach(function(key) {
 if (exampleTemplates[key] && exampleTemplates[key].body && exampleTemplates[key].body.rows) {
 // Check if first row already has an image (logo) element - skip if so
 var firstRow = exampleTemplates[key].body.rows[0];
 var hasImage = false;
 if (firstRow && firstRow.columns) {
 firstRow.columns.forEach(function(col) {
 if (col.contents) {
 col.contents.forEach(function(c) {
 if (c.type === "image") hasImage = true;
 });
 }
 });
 }
 if (!hasImage) {
 // Only for product_launch which doesn't clone from welcome
 var bgColor = templateLogos[key];
 var logoRow = makeLogoRow(bgColor, key + "_logo");
 exampleTemplates[key].body.rows.unshift(logoRow);
 if (exampleTemplates[key].counters) {
 exampleTemplates[key].counters.u_content_image = (exampleTemplates[key].counters.u_content_image || 0) + 1;
 exampleTemplates[key].counters.u_row = (exampleTemplates[key].counters.u_row || 0) + 1;
 exampleTemplates[key].counters.u_column = (exampleTemplates[key].counters.u_column || 0) + 1;
 }
 } else {
 // Update the existing logo image URL to the placeholder
 firstRow.columns.forEach(function(col) {
 if (col.contents) {
 col.contents.forEach(function(c) {
 if (c.type === "image" && c.values && c.values.src) {
 c.values.src.url = logoUrl;
 }
 });
 }
 });
 // Also update the logo row's background color to match the template theme
 firstRow.values.columnsBackgroundColor = templateLogos[key];
 }
 }
 });
})();
</script>
