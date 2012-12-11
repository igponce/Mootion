javascript:
w=window;
d=document;
var u;
s='';
ds=d.selection;
if(ds&&ds!=u){
    if(ds.createRange()!=u){
        s=ds.createRange().text;
    }
}else if(d.getSelection!=u){
    s=d.getSelection()+'';
}else if(w.getSelection!=u){
    s=w.getSelection()+'';
} if(s.length<2){
    h=String(w.location.href);
    if(h.length==0||h.substring(0,6)=='about:'){
        s=prompt('Search for videos in mOOtion.com:',s);
    }else{
        s=w.location.href;
    }
}

if(s!=null)
    w.location='http://mootion.com/video_submit.php?&url='+escape(s);void(1);
