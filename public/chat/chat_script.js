
class ChatApp{

    constructor() {
        var firebaseConfig = {
            apiKey: "AIzaSyDWMXFq9Uwoqta2G8SCwc8o0trE-yHPke0",
            authDomain: "biponi-68135.firebaseapp.com",
            projectId: "biponi-68135",
            storageBucket: "biponi-68135.appspot.com",
            databaseURL: "https://biponi-68135.firebaseio.com/",
            messagingSenderId: "397239095845",
            appId: "1:397239095845:web:7c269ece0c25053d3de5cc",
            measurementId: "G-1BK3HRWTD3"
        };
        firebase.initializeApp(firebaseConfig);

        let db = firebase.firestore();
        this.ChatRooms = db.collection("ChatRooms");
        this.ChatRoomUsers = db.collection("ChatRoomUsers");
    }


    

    getChat(){
        let docID = localStorage.getItem('sellerID')+localStorage.getItem('userID');
       // this.ChatRooms.doc(docID).collection("messages").orderBy("timestamp").onSnapshot(function(snapshot){
        this.ChatRooms.doc(docID).collection("messages").orderBy("timestamp").get().then((snapshot) => {
            let htmlMessage = '';
            snapshot.docChanges().forEach(function(value,index){
                let data = value.doc.data();
                //console.log(data);
                
                if(data.senderId == localStorage.getItem('userID')){
                    htmlMessage +=  '<div class="customer_message"><p>'+data.text+'</p></div>';
                }else{
                    if(jQuery('.selected_chat').attr('data-seller-id') != data.senderId){
                        jQuery('div[data-seller-id="'+data.senderId+'"]').addClass('new_message');
                    }else{
                        htmlMessage +=  '<div class="seller_message"><p>'+data.text+'</p></div>';
                    }
                }
            });
            jQuery('.comment_section').append(htmlMessage);
            jQuery('.chating_title_dynamic').html(localStorage.getItem('sellerName'));
            jQuery("#comments_section").animate({ scrollTop: $('#comments_section').prop("scrollHeight")}, 1000);

        });
    }

    initChatList(){
        let docID = localStorage.getItem('sellerID')+localStorage.getItem('userID');
        //let audio = new Audio('http://127.0.0.1:8003/chat/sound.mp3');
        this.ChatRooms.doc(docID).collection("messages").orderBy("timestamp").onSnapshot(function(snapshot){
            let htmlMessage = '';
            snapshot.docChanges().forEach(function(value,index){
                let data = value.doc.data();
                //console.log(data);
                
                if(data.senderId == localStorage.getItem('userID')){
                    htmlMessage +=  '<div class="customer_message"><p>'+data.text+'</p></div>';
                }else{
                    if(jQuery('.selected_chat').attr('data-seller-id') != data.senderId){
                        jQuery('div[data-seller-id="'+data.senderId+'"]').addClass('new_message');
                        //audio.play();
                    }else{
                        htmlMessage +=  '<div class="seller_message"><p>'+data.text+'</p></div>';
                       // audio.play();
                    }
                }
            });
            jQuery('.comment_section').append(htmlMessage);
            jQuery('.chating_title_dynamic').html(localStorage.getItem('sellerName'));
            jQuery("#comments_section").animate({ scrollTop: $('#comments_section').prop("scrollHeight")}, 1000);

        });
    }


    initChat(){
        this.ChatRoomUsers.get().then((snapshot) => {
        
            let allData = snapshot.docs.map(doc => doc.data());
            let formatedHtml = '';
            let selectedChat = '';
            let avater = '';
            let host = window.location.protocol + "//" + window.location.host;

            allData.forEach(function(val,key){

                if(val != null){
                    if(val.usersid[1].toString() == localStorage.getItem('userID').toString()){

                        if(val.usersid[0] == localStorage.getItem('sellerID')){
                            selectedChat = val.tousername;
                        }
        
                        let LocalDate = val.timestamp.toDate().toLocaleDateString('en-US');
                        let LocalTime = val.timestamp.toDate().toLocaleTimeString('en-US');
                        if(!val.toUserphoto !=''){
                            avater = host+'/chat/avater.png';
                        }else{
                            avater = localStorage.getItem('chatImageHead')+'/'+val.toUserphoto;
                        }
                        formatedHtml+='<div data-seller-id="'+val.usersid[0]+'" data-seller-name="'+val.tousername+'" class="row seller_section">'+
                                '<div class="col-md-2 col-lg-2 p-0">'+
                                '<img style="width:80%" src="'+avater+'" alt="avater">'+
                                '</div>'+
                                '<div class="col-md-10 col-lg-10">'+
                                '<span class="seller_item_title">'+val.tousername+'</span> <p>'+LocalDate+' '+LocalTime+'</p>'+
                                '</div>'+
                            '</div>';
                    }
                }

            });

            setTimeout(function(){ 
                jQuery('#chat_rooms_list').html(formatedHtml);
                jQuery('.comment_section').html('<div><p class="empty_message"><span class="empty-icon"><svg width="55" height="47"><defs><filter x="-1.4%" y="-2%" width="102.9%" height="104%" filterUnits="objectBoundingBox" id="mailbox_svg__a"><feOffset in="SourceAlpha" result="shadowOffsetOuter1"></feOffset><feGaussianBlur stdDeviation="3" in="shadowOffsetOuter1" result="shadowBlurOuter1"></feGaussianBlur><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.2 0" in="shadowBlurOuter1" result="shadowMatrixOuter1"></feColorMatrix><feMerge><feMergeNode in="shadowMatrixOuter1"></feMergeNode><feMergeNode in="SourceGraphic"></feMergeNode></feMerge></filter></defs><g filter="url(#mailbox_svg__a)" transform="translate(-431 -102)" fill="none" fill-rule="evenodd"><path d="M435 103h48v48h-48z"></path><path d="M475.052 108.486h-33.164c-2.28 0-4.124 1.943-4.124 4.318l-.021 25.909c0 2.375 1.865 4.318 4.145 4.318h33.164c2.28 0 4.145-1.943 4.145-4.318v-25.91c0-2.374-1.865-4.317-4.145-4.317zm0 8.636l-16.582 10.796-16.582-10.796v-4.318l16.582 10.795 16.582-10.795v4.318z" fill="#2ca8e3" data-spm-anchor-id="a2a0e.pdp.1362278930.i11.2864PcoIPcoIQl"></path></g></svg></span></p><p class="text-center">Once you start a new conversation, you’ll see it listed here.</p></div>')
                jQuery('.chating_title_dynamic').html(selectedChat);
                jQuery("#comments_section").animate({ scrollTop: $('#comments_section').prop("scrollHeight")}, 1000);
            }, 3000);
        }).catch((error) => { console.log(error); });
    }

    sendMessage(object){

        if(localStorage.getItem('sellerID') == null){
            alert('Please select a seller to generate chat!');
            return;
        }
        
        let docID = localStorage.getItem('sellerID')+localStorage.getItem('userID');
        let useDetails = {
            fromusername : localStorage.getItem('userName'),
            fromuserphoto : localStorage.getItem('imageUrl'),
            id: docID,
            senderId : localStorage.getItem('userID'),
            text : object.text,
            timestamp : firebase.firestore.Timestamp.now(),
            toUserId : localStorage.getItem('sellerID'),
            toUserphoto: '',
            tousername: localStorage.getItem('sellerName'),
            usersid: [localStorage.getItem('sellerID'),localStorage.getItem('userID')],
            seenbyseller : 0,
            seenbyuser : 1
        };

        this.ChatRoomUsers.doc(docID).set(useDetails).then(added => {
            //console.log("chat room user added");
        }).catch(err => {
            console.err("Error occured",err)
        });


        this.ChatRooms.doc(docID).collection("messages").add(object).then(added => {
            //console.log("message sent ",added)
        }).catch(err => {
            console.err("Error occured",err)
        });

    }

    deleteMessage(doc_id){
        var flag = window.confirm("Are you sure to want delete ?")
        if(flag){
            db.collection("chats").doc(doc_id).delete();
            //console.log("Deleted");
        }
    }

}


var chatClass = new ChatApp();

jQuery(document).on('click','#btn-chat',function(){
    var message = $('#btn-input').val();
    if(message){
        chatClass.sendMessage({
            senderId : localStorage.getItem('userID'),
            sendername : localStorage.getItem('userName'),
            text : message,
            imageUrl : localStorage.getItem('imageUrl'),
            toUserId : localStorage.getItem('sellerID'),
            timestamp : firebase.firestore.Timestamp.now(),
        });

        $('#btn-input').val("")
    }

})


jQuery(document).on('keyup','#btn-input',function(){
    if(event.keyCode == 13){ 
       var message = $('#btn-input').val();
        if(message){
            chatClass.sendMessage({
                senderId : localStorage.getItem('userID'),
                sendername : localStorage.getItem('userName'),
                text : message,
                imageUrl : localStorage.getItem('imageUrl'),
                toUserId : localStorage.getItem('sellerID'),
                timestamp : firebase.firestore.Timestamp.now(),
            });
            $('#btn-input').val("")
        }
    }
});


jQuery(document).on('click','.seller_section',function(){
    jQuery('.seller_section').removeClass('selected_chat');
    jQuery(this).addClass('selected_chat');
    jQuery(this).removeClass('new_message');
    jQuery('.message_input').show();
    localStorage.setItem('sellerID',jQuery(this).attr('data-seller-id'));
    localStorage.setItem('sellerName',jQuery(this).attr('data-seller-name'));
    jQuery('.comment_section').html('');
    chatClass.getChat();
    
});



jQuery(document).on('click','.chating-box-product',function(){

    if (confirm("Are you sure to generate a chat with this seller?")) {
        jQuery('.chating_wrapper').fadeIn();
        localStorage.setItem('sellerID',jQuery(this).attr('data-seller-id'));
        localStorage.setItem('sellerName',jQuery(this).attr('data-seller-name'));
        localStorage.setItem('imageUrl',jQuery(this).attr('data-product-image'));
    
        var message = '<p>'+jQuery(this).attr('data-product-title')+'<br><img width="100" src="'+jQuery(this).attr('data-product-image')+'"></p>';
        if(message){
            chatClass.sendMessage({
                senderId : localStorage.getItem('userID'),
                sendername : localStorage.getItem('userName'),
                text : message,
                imageUrl : localStorage.getItem('imageUrl'),
                toUserId : localStorage.getItem('sellerID'),
                timestamp : firebase.firestore.Timestamp.now(),
            });
            
            chatClass.initChat();
        }
    } 

});


jQuery(document).ready(function(){
    chatClass.initChat();
    chatClass.initChatList();
});



