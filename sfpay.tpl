<div class="card-inner">
    <p class="card-heading">输入充值金额后，点击下方的图标进行充值</p>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount">金额</label>
        <input class="form-control" id="amount" type="text">
    </div>
</div>
<div id="qrarea">
    <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('Alipay')">
        <img src="data:image/svg+xml;base64,CjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPG1ldGFkYXRhPiDnn6Lph4/lm77moIfkuIvovb0gOiBodHRwOi8vd3d3LnNmb250LmNuLyA8L21ldGFkYXRhPjxnPjxwYXRoIGQ9Ik05OTAsNjgwLjlWMTk4LjVDOTkwLDk0LjQsOTA1LjcsMTAsODAxLjUsMTBIMTk4LjVDOTQuNCwxMCwxMCw5NC4zLDEwLDE5OC41djYwMy4xQzEwLDkwNS42LDk0LjMsOTkwLDE5OC41LDk5MGg2MDMuMWM5Mi44LDAsMTY5LjktNjcsMTg1LjUtMTU1LjNjLTUwLTIxLjUtMjY2LjctMTE1LjEtMzc5LjQtMTY5Yy04NS44LDEwNC0xNzUuOCwxNjYuNS0zMTEuMywxNjYuNXMtMjI2LTgzLjMtMjE1LjEtMTg1LjZjNy4xLTY3LjIsNTMuMi0xNzYuNiwyNTMtMTU3LjhjMTA1LjMsMTAsMTUzLjUsMjkuNSwyMzkuNCw1Ny45YzIyLjEtNDAuNyw0MC42LTg1LjUsNTQuNi0xMzMuMkgyNDcuNXYtMzcuN2gxODguM3YtNjcuOEgyMDZ2LTQxLjVoMjI5Ljh2LTk3LjhjMCwwLDIuMi0xNS4zLDE5LTE1LjNoOTQuM3YxMTMuMWgyNDV2NDEuNWgtMjQ1djY3LjhoMTk5LjdjLTE4LjMsNzQuOC00Ni4yLDE0My41LTgxLDIwMy41QzcyNS45LDYwMC4yLDk5MCw2ODAuOSw5OTAsNjgwLjlMOTkwLDY4MC45TDk5MCw2ODAuOXogTTI4MS40LDc2Ny42Yy0xNDMuMywwLTE2NS44LTkwLjUtMTU4LjMtMTI4LjJzNDktODYuNywxMjguNi04Ni43YzkxLjUsMCwxNzMuNSwyMy40LDI3MS44LDcxLjNDNDU0LjUsNzE0LDM2OS41LDc2Ny42LDI4MS40LDc2Ny42TDI4MS40LDc2Ny42eiIgc3R5bGU9ImZpbGw6IzU2YWJlNCI+PC9wYXRoPjwvZz48L3N2Zz4gIA=="
             width="64" height="64">
    </button>
    <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('WEPAY')">
        <!-- iCon by SFont.Cn -->
        <img src="data:image/svg+xml;base64,CjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPG1ldGFkYXRhPiDnn6Lph4/lm77moIfkuIvovb0gOiBodHRwOi8vd3d3LnNmb250LmNuLyA8L21ldGFkYXRhPjxnPjxwYXRoIGQ9Ik0zMTIuNiwzMTUuN2MtMTkuMSwwLTM4LjMsMTIuNi0zOC4zLDMxLjhjMCwxOSwxOS4yLDMxLjcsMzguMywzMS43YzE5LjEsMCwzMS43LTEyLjgsMzEuNy0zMS44QzM0NC4zLDMyOC4yLDMzMS43LDMxNS43LDMxMi42LDMxNS43TDMxMi42LDMxNS43TDMxMi42LDMxNS43eiBNNDkwLjMsMzc5LjFjMTkuMiwwLDMxLjgtMTIuOCwzMS44LTMxLjdjMC0xOS4xLTEyLjYtMzEuOC0zMS44LTMxLjhjLTE5LDAtMzguMSwxMi42LTM4LjEsMzEuOEM0NTIuMywzNjYuNCw0NzEuNCwzNzkuMSw0OTAuMywzNzkuMUw0OTAuMywzNzkuMUw0OTAuMywzNzkuMXogTTU3Mi45LDUwMGMtMTIuNiwwLTI1LjQsMTIuNi0yNS40LDI1LjNjMCwxMi44LDEyLjgsMjUuNCwyNS40LDI1LjRjMTkuMiwwLDMxLjgtMTIuNiwzMS44LTI1LjRDNjA0LjcsNTEyLjYsNTkyLjIsNTAwLDU3Mi45LDUwMEw1NzIuOSw1MDBMNTcyLjksNTAweiBNNzEyLjcsNTAwYy0xMi42LDAtMjUuMywxMi43LTI1LjMsMjUuNGMwLDEyLjgsMTIuOCwyNS40LDI1LjMsMjUuNGMxOS4xLDAsMzEuOC0xMi42LDMxLjgtMjUuNEM3NDQuNSw1MTIuNiw3MzEuOCw1MDAsNzEyLjcsNTAwTDcxMi43LDUwMEw3MTIuNyw1MDB6IE04MDEuNSwxMEgxOTguNEM5NC40LDEwLDEwLDk0LjQsMTAsMTk4LjR2NjAzLjJDMTAsOTA1LjYsOTQuMyw5OTAsMTk4LjQsOTkwaDYwMy4xYzkyLjcsMCwxNjkuOC02NywxODUuNS0xNTUuMmwyLjktMTUzLjlWMTk4LjRDOTkwLDk0LjQsOTA1LjYsMTAsODAxLjUsMTBMODAxLjUsMTBMODAxLjUsMTB6IE0zOTUuMiw2MzkuOGMtMzEuNywwLTU3LjItNi40LTg4LjktMTIuN2wtODguOCw0NC41bDI1LjQtNzYuNGMtNjMuNi00NC41LTEwMS43LTEwMS44LTEwMS43LTE3MS41YzAtMTIwLjksMTE0LjQtMjE2LDI1NC4xLTIxNmMxMjQuOSwwLDIzNC4zLDc2LjEsMjU2LjMsMTc4LjRjLTguMi0wLjktMTYuNC0xLjUtMjQuNS0xLjVjLTEyMC43LDAtMjE1LjksOTAtMjE1LjksMjAxYzAsMTguNiwyLjksMzYuNCw3LjgsNTMuM0M0MTEsNjM5LjQsNDAzLjEsNjM5LjgsMzk1LjIsNjM5LjhMMzk1LjIsNjM5LjhMMzk1LjIsNjM5Ljh6IE03NjkuNyw3MjguOGwxOS4yLDYzLjVsLTY5LjctMzguM2MtMjUuNCw2LjQtNTAuOSwxMi43LTc2LjIsMTIuN2MtMTIwLjksMC0yMTYtODIuNS0yMTYtMTg0LjNjMC0xMDEuNiw5NS4xLTE4NC4zLDIxNi0xODQuM2MxMTQuMSwwLDIxNS44LDgyLjgsMjE1LjgsMTg0LjNDODU4LjgsNjM5LjgsODIwLjgsNjkwLjUsNzY5LjcsNzI4LjhMNzY5LjcsNzI4LjhMNzY5LjcsNzI4Ljh6IiBzdHlsZT0iZmlsbDojMTFjZDZlIj48L3BhdGg+PC9nPjwvc3ZnPiAg"
             width="64" height="64">
    </button>
      <button class="btn btn-flat waves-attach" id="qqpay" name="qqpay" onclick="pay('QQPAY')">
        <!-- iCon by SFont.Cn -->
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAABJ0AAASdAHeZh94AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH4gkFDxQF+A7mjQAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxOC0wOS0wNVQxNToyMDowNSswODowMCjg1egAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMTgtMDktMDVUMTU6MjA6MDUrMDg6MDBZvW1UAAAL5ElEQVR4XtWbe3AV1R3Hv7t7H7l5kneIoAQIEN6x0CEDOkOwCE47ZRjtDA87MFNRhNY6xaIWmGnVmY7yB40ljMgIdlodqUpbOwacFsqACFILhloTipUIhDxICAnc5D63v9/ec+PNvefeu5sHiZ+Z391z9rx+v9+ePXvO7rmKTmCIOX36ND788EPU1tbi/PnzaGxsxPXr19Hd3W2ku1wuZGdno7i4GKWlpZg1axbmz5+PuXPnGulDCjtgKNi9e7c+bdo0du6AZOrUqUZdQ8WgO+Cxxx6TGjIY8uijj4pWBo9Bc8D27dulSg+FcFuDxaCMAXzvXr16VcRuD6NHjzbGkoEyIAe0tLSgsLBQxIaH5uZmFBQUiJh1VHG0zOXLl4fdeIZ1uHTpkohZp189wOv1wul0itjIgHWy2+0iZp5+9QCzxk+ZMgUNDQ080MLv92Pjxo0iJT6ch/NyGS7LdZjB4XCIkEW4B1jhoYce6h2NE8nMmTNFib68/vrr0vwsnCZjxowZ0vzRwrpZxZID6urqpA3LJBHsnOj88RwWJjp/PKmvrxclzGHJAbm5udJGo2X27NmixNe8+OKL+o4dO4xwTU1NTJn333/fSIsHTY9jysgkLy9PlDCHaQccPXpU2qBMomdsa9as6U178sknjXOR+VncbrdxfteuXfqECRP01NRUfc6cOfrbb79tnOc6o8vEE9bVLKYdUFZWJm1MJuvWrROlQmRlZfWm0QTGOBeZn4XhNFVV9T179uj79+/XFy9erFdWVhppjzzySEyZeMLrB7OYcgBfHVlD8WT69OmiZIgNGzb0pm3atEmnyUuf/CwXL17Ujxw5IkrEwnVGl0kktNIUJRNjygHPPfectJFEEg137erqaiP8xBNPxOTnc4mIzp9MWGczmHJASUmJtJFEUl5eLkr3xefzSfOzcJoMrkuWP5GMHz9elE6MKQfIGjAjFRUVooYQtbW10nyRwnki4Tpk+cyIGZLmOnXqlLRyK5KTkyM9n0j6UyZaPv74Y2FFfJJOhflV1kBpb283jpmpQAkt3GaNUzBvkoIFZSHhMJ/jtCzKw4TLDIQTJ06IUHySLobWrl2Lffv2iVhyRmcDi2erqJisYnaJgrIxCjILFMBGiUEhTHSrlMWALwmLH+hq0fGfyzo+/VLHR/VBfHA2iMbrRi5T0PwDe/fuFTE5SR2wYMECU71g5zoNj3+XrEyniIeEDECA7CTxh422iI0coWgUYGEH8hrsJlD9Vz827KaKk8C6Hzt2TMTkJL0FzLx1+dF9Kh5fT0tRUtJPPddHSvp6SHz9N57hslyHURfVyXVzG9wWt5mMK1euiFB8ktbS0dEhQvHZ87cg1Lt78MpfArBlAnYWWp2Ge/VA4Dq4Lq6T6979XgAatcVtJsOM7klvgbS0NNBMUMTMsezbKjY8oOG+BeRfrt1LQrdEkCSQRG+Niqjc5fndBi/xyQN/Px7EzpoADpyy1p1Y95s3qeskIKkDMrOy0NXZKWLW+dYEBd+bo6JyBg2MU1TY8ugk375sS7hlvszcF8nwwDUavWnAO3IuiPdOB/HPLxKql5CMzEx03rghYnKS94CiIribm0VscMihgbIoW0FGSijeRfd403Ud7YkvlmVSCwtxq6lJxOQkdkCQLpMjB+dysnDY243jJB95unElmHwEHg6K6d6pcLhwj9OFhXSc2U5X30sjpxp/qEvogK6fbELPvt/DkZ4Oh6LQLalAoyNzwe/FGZ8H50g+p3Cdz0vnfOiJecAPLk7SodRmx2SbA1NpdJxhd2I2SSnFmSCZ4yEdvHT00v2fsmY1Mqq2G2kyEjqgWXFBLSqGIoyOhB/LdlLGRmkcDjuGb+h2Gu0uBXxoDATQFPDjGvWYdpIOPYib1KvcrCSJTziL63FS+VSSdLpaoxQVOXQ180gKNRuKNQ13anbjXBg2lJ6Q8It6eNoRDZsWbGpEoR76CCsjrgNubvkV3Dt+C5UGEquI8YxEgUp+4bhK4fBYZ7iKfsIuMxSgHz6Gx8Yg/XI4SJEAhcPjplWCNICn/nQj0p/fJs70Ja4DmrVMqPl5UBLcP98EdOpxwdZrKAzIn2RS6269tAOKk+62b7jxDNvAtrBNMuQ9IK0APur6HronvaIrfhPhy8cDt5NMtPNc5lZLKCGCmEv8m6oqFLhbsamrDUc8bmNwylNtyCVPZtDglEIV8v0d584ZFlgX1ol1Yx1ZV9Y5hXT/h9eNp8iWQrKJbYsmpgfIRnyueAE9WyscKZhrT0E5Ve661oYA57XboebQGlhSbjAx1KSniiH86cwfOrJodJ/35OXiDD19TtPK6SNvD47RfEX2SI6+cH0ccOjQISxZskTEEpCZAf1GJwINlxD435foeGA51FFZSZ2g95BKHlorG/mi85IarAoLD/08CSPhQcxYU9O6WLtzDLSSEmil42GbVAqtbDJs08qgjbsLSja135F8yn7w4EHcf//9IhblAP4QWV9fL2LxWbhwIQ4fPixiPF9Ig0ZT5kQOYOMdS74D+z3zodMqTfd6e13ACigOultTqK+lp0Gh9Qf3KoWeQmpRIbTC5N//Fy1a1EeneEyePBl1dXUiRrADmLa2NtbDlPA+oEiakKq3Fo3XW0dPiCvNqfn6rapdosTgs379eqmuMmlvbxelIt4Jbt26VYSSM3bsWBGyCF31ocKKTlu2bBGhiKdAdXW1CCUnNzdXhEYOVnSKtNVwgNU3vxkZGSI0ckinBZsVwjYbDti5c6cRMYs6AmeIGi2YrBC22bDkzTffNCJm4f04Iw2rOoVtVnmrm1U6Y16Rhfb0sAwXsTolp7W1FWpNTY2Imof35kWiFhRDb2s31t6Bq00INrcg2NL6tXCcpqLBzi5RYvCJ1skMbLuaaAB85plnRKgvFy5cEKEQ+c1fosDXYbx4yL9+GbkXapHz6Ulkf3LcOOZ+cQ4FZHz6L38hSgw+0TqFefbZZ0UoluPHjwO8DYXCMfLZZ5/p7777rjSNt7CMNCZOnCjV9cCBA4YtsjTD9uzs7JiEF154waiUd21Ep4VlpCHTkYVtYNim6DTxBbrvyWXLlhkFwkSnh+XkyZMix/CT6BN+JGxbTB56fvZGojc0MPH2/69YsULkGH5Wrlwp1ZHXB9FEbriw2Ww6/0XFiCxdulRk6UuiLS0jBZluLPG23LCtnD5p0iSdPx/rNCkQSXJWrVoVUznL5s2bRY7h4+mnn5bqtnr1apFDDttMTwHzf5iQvSliTBYfMgaql+lJ/dmzZ0WoL3fccYcI3X7GjBkjQn2Jp6sU7gFmqaqq6u1ikbJ8+XKR4/bx4IMPSnV5+eWXRQ5zWB7Jtm3bJm2Yx4nbxcMPPyzVgXWzSr+G8ldffVWqAO/rH2pkW+1ZWKf+0C8HMOfPHJQqwvLWW2+JXIMHb56WtcVSf+YDkcs6lhwQvHVF9zQc0DuOrtX1Pzl1/ZBNX3WvKlUqJSVFf+2110TJ/rN3717d5XJJ21hJbesfkB77oXv323X/Jz/Wg+4roqQ5TD0G/Tf+C/+nm+DvboOuuqh1DXb353D4GqGN0tDWqOMH2304fE5e1bx580CDFiorK1FeXi7OyuERnF9vv/POO3E3Oi6cruCPT9mRW6wgcCP0GUE3NiAFjP1ISv5E2OYfgJI1XZSIT1IHBOpeQuDEz6HwDs7wI5ePio0k9BrKzodMoKdNx9Y3Atj+Z1LEBOH3eMk2MoX52fc1PL9SQ0ouKdAJ+KTNUK8OkBduAVrFr6GVbRbn5SR1gO8PCt0mFOBdEIbh/EPThz5fd0JHdoTC+37SgIa6IN445kfNv4BjnxvJlrlnCrD0bmDFvTaMm0JtklE67xnsNTysOncBDvNXJA5TkHdMkFoOYxIbn6QO0APdCF78HXDj39DdX0H3tFI366CuQdoEPNQmeVvn78cs7BgS1R76D5/LCTjJIy7qPp5UNDSl46uODLS6M9EZyIZHKzLacAZakGlrQ76rE2NHdWFcEfUIpxvoJvGQxd0e0LyemiCJactBl5ra0cjrjlFQnPnUW+8EsqZBveuH1FHF5mMpwP8Bx/JAjLUsXG8AAAAASUVORK5CYII="
             width="64" height="64">
    </button>
  
</div>
<script>
   if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
             $("#qqpay").hide();
          
            } 
 function Post(URL, PARAMTERS) {
             var temp_form = document.createElement("form");
             temp_form.action = URL;
             temp_form.target = "_blank";
             temp_form.method = "post";
             temp_form.style.display = "none";
             for (var item in PARAMTERS) {
                 var opt = document.createElement("textarea");
                 opt.name = PARAMTERS[item].name;
                 opt.value = PARAMTERS[item].value;
                 temp_form.appendChild(opt);
             }
             document.body.appendChild(temp_form);
             temp_form.submit();
         }
  
 function pay(type) {
        if (type === 'Alipay') {
            if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                type = '904';
            } else {
                type = '903';
            }
        }
       if (type === 'QQPAY') {
          
                type = '908';
            
        }
        if (type === 'WEPAY') {
            if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                type = '901';
            } else {
                type = '902';
            }
        }

        var price = parseFloat($$getValue('amount'));
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '非法的金额！'
            return;
        }

             var path= window.location.protocol+'//' + window.location.host;   
             var parames = new Array();
             parames.push({ name: "price", value: price});
             parames.push({ name: "type", value: type});
             Post(path+"/user/payment/purchase",parames);
         

    }


</script>
