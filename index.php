<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="icon" href="done.png" type="image/x-icon">
    <link rel="stylesheet" href="lightstyle.css">
</head>

<?php
session_start();
if(!isset($_SESSION['email'])){
    header("location: login.html");
    exit;
}
?>

<body>
    <div id="maindiv">
        <div id="column-content">
            <div id="welcome">
                Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
            </div>
            <div id="todo">
                <input type="image" src="dn.png" alt="dn-icon" id="dn" value="light">
                <input type="image" src="logout.png" alt="logout-icon" id="logout" value="register">
                <h1>To Do</h1>
                <input type="text" name="task" id="task" placeholder="add new task">
                <button id="plusbtn" value="add">+</button>
                <br>
                <input type="date" name="dat" id="dat">
                <br>
                <label for="" id="wp">0 works pending...</label>
                <button id="clearbtn" value="clear">Clear all</button>
                <ul id="list"></ul>
            </div>
        </div>
    </div>
    <script>
        let i = 0;

        function checkinput(){
            const dat=new Date(deadline.value).setHours(0,0,0,0);
            if(task.value==="" || task.value.length>25 || deadline.value==="" || dat< currdat){
                plusbtn.disabled=true;
                plusbtn.style.cursor='not-allowed';
            }else{
                plusbtn.disabled=task.value.trim()==="";
                plusbtn.style.cursor='pointer';
            }
            plusbtn.style.backgroundColor=plusbtn.disabled ? "rgba(153, 86, 216, 0.623)" : "purple";
        }

        function check(){
            const dat=new Date(deadline.value).setHours(0,0,0,0);
            if(task.value.length>25 || deadline.value==="" || dat< currdat){
                return false
            }else{
                return true
            }
        }

        function updateclearbtn(){
            clearbtn.disabled= i===0;
            clearbtn.style.cursor= i===0 ? 'not-allowed' : 'pointer';
            clearbtn.style.backgroundColor= i===0 ? "rgba(153, 86, 216, 0.623)" : "purple";
        }


        function addtaskstorage(taskcontent,deadlinecontent){
            const li=document.createElement("li");
            
            const text=document.createElement("span");
            text.textContent=taskcontent;
            text.className = "textspan";

            const deaddat=document.createElement("span");
            deaddat.textContent=deadlinecontent;
            deaddat.className='spandat';
            

            const img=document.createElement("img");
            img.src='del.png';
            img.className='delete'
            img.addEventListener('click', ()=>{
                fetch("delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `task=${encodeURIComponent(taskcontent.trim())}&deadline=${encodeURIComponent(deadlinecontent)}`
                })
                .then(res=>res.text())
                .then(data=>{
                    console.log(data)
                    if(data==="task deleted successfully!"){
                        li.remove();
                        i--;
                        updateclearbtn();
                        document.getElementById('wp').textContent=`${i} works pending...`;
                    }
                })
                .catch(err=>{
                console.error("Network error:", err);
                alert("Network error. Please try again.");
                });
                
            });
            
            li.appendChild(text);
            li.appendChild(deaddat);
            li.appendChild(img);
            document.getElementById('list').appendChild(li);
        
            i++;

            updateclearbtn();
            document.getElementById("wp").textContent = `${i} works pending...`;
            
        }
        // enable the plus button
        const task=document.getElementById('task');
        const deadline=document.getElementById('dat');
        const plusbtn=document.getElementById('plusbtn');
        const currdat=new Date().setHours(0,0,0,0);
        
        task.addEventListener("input", checkinput);
        deadline.addEventListener("input", checkinput);

        // add new tasks with deadline
        plusbtn.addEventListener('click', () =>{
        if(check()===true){
            fetch("sent.php",{
                method:"POST",
                headers:{
                    "Content-Type":"application/x-www-form-urlencoded",
                },
                body:`task=${encodeURIComponent(task.value.trim())}&deadline=${encodeURIComponent(deadline.value)}&`
            })
            .then(res=> {
                if(res.status===200){
                    return res.text();
                }
                else if(res.status===401){
                    alert("you're not logged in!");
                    window.location.href="login.html";
                }
                else if(res.status===404){
                    alert("Something went wrong. Status: " + res.status);
                }
                else{
                    alert("Bad request. Check your input");
                }
            })
            .then(data=>{
                if(data){
                    console.log("Server replied:", data);
                }
            })
            .catch(err=>{
                console.error("Network error:", err);
                alert("Network error. Please try again.");
            });

            addtaskstorage(task.value.trim(), deadline.value);
            task.value = "";
            deadline.value = "";
            checkinput();
        }
        });
        // clear
        const clearbtn=document.getElementById('clearbtn');
        
        clearbtn.addEventListener('click', () =>{
            fetch('delete_all.php')
            .then(res=>res.text())
            .then(data=>{
                console.log(data);
                if(data==="all tasks deleted successfully!"){
                    document.getElementById('list').innerHTML="";
                    i=0;
                    updateclearbtn();
                    document.getElementById('wp').textContent=`${i} works pending...`;   
                }
            })
            .catch(err=>{
                console.error("Network error:", err);
                alert("Network error. Please try again.");
            });    
        });

        const dn=document.getElementById('dn');
        const todo=document.getElementById("todo");
        
        dn.addEventListener('click', () =>{
            if(dn.value==='light'){
                dn.value="dark";
                const link=document.createElement("link");
                link.rel="stylesheet";
                link.href="darkstyle.css";
                document.head.appendChild(link);
                localStorage.setItem("theme","dark");
            }else{
                dn.value="light";
                const link=document.createElement("link");
                link.rel="stylesheet";
                link.href="lightstyle.css";
                document.head.appendChild(link);
                localStorage.setItem("theme","light");
            }
        });
        // login
        const logout=document.getElementById("logout");
        logout.addEventListener("click", ()=>{
            window.location.href="login.html"
        })

        //load page
        window.addEventListener('DOMContentLoaded',()=>{
            const savedtheme=localStorage.getItem("theme");
            const link=document.createElement("link");
            link.rel="stylesheet";
            link.href=savedtheme === 'dark' ? 'darkstyle.css' : 'lightstyle.css';
            document.head.appendChild(link);
            dn.value=savedtheme;
            
            checkinput();

            fetch('get.php')
            .then(res=>res.text())
            .then(data=>{
                if(data!=""){
                    const lines=data.trim().split('\n');
                    lines.forEach(line=>{
                        const [task,deadline]=line.split('|');
                        addtaskstorage(task,deadline);
                    });
                }
            })
            .catch(err=>{
                console.error("Network error:", err);
                alert("Network error. Please try again.");
            });
        });
        


        
        
        
    </script>
</body>
</html>