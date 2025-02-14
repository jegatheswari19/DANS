

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @mixin aspect-ratio($width, $height) {
          position: relative;
            
          &:before {
            display: block;
            content: "";
            width: 100%;
            padding-top: ($height / $width) * 100%;
          }
            
          > img {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
          }
        }
        
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #F4F4F4;
        }

        header {
            background: #003366;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .logo {
            display: inline-block;
            vertical-align: middle;
        }

        .logo img {
            width: 100px;
            height: 100px;
        }

        .university-name {
            display: inline-block;
            vertical-align: middle;
            font-size: 24px;
            font-weight: bold;
            margin-left: 20px;
        }

        .university-name span {
            color: #FFD700;
        }

        section {
            background: #E0FFFF;
            padding: 50px 0;
        }
        
        .container {
            max-width: 1044px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .carousel {
            display: block;
            text-align: left;
            position: relative;
            margin-bottom: 22px;
            
            > input {
                clip: rect(1px, 1px, 1px, 1px);
                clip-path: inset(50%);
                height: 1px;
                width: 1px;
                margin: -1px;
                overflow: hidden;
                padding: 0;
                position: absolute;
                
                &:nth-of-type(6):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: -500%; }
                &:nth-of-type(5):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: -400%; }
                &:nth-of-type(4):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: -300%; }
                &:nth-of-type(3):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: -200%; }
                &:nth-of-type(2):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: -100%; }
                &:nth-of-type(1):checked ~ .carousel__slides .carousel__slide:first-of-type { margin-left: 0%; }
                
                &:nth-of-type(1):checked ~ .carousel__thumbnails li:nth-of-type(1) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
                &:nth-of-type(2):checked ~ .carousel__thumbnails li:nth-of-type(2) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
                &:nth-of-type(3):checked ~ .carousel__thumbnails li:nth-of-type(3) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
                &:nth-of-type(4):checked ~ .carousel__thumbnails li:nth-of-type(4) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
                &:nth-of-type(5):checked ~ .carousel__thumbnails li:nth-of-type(5) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
                &:nth-of-type(6):checked ~ .carousel__thumbnails li:nth-of-type(6) { box-shadow: 0px 0px 0px 5px rgba(0,0,255,0.5); }
            }
        }
        
        .carousel__slides {
            position: relative;
            z-index: 1;
            padding: 0;
            margin: 0;
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
            display: flex;
        }
        
        .carousel__slide {
            position: relative;
            display: block;
            flex: 1 0 100%;
            width: 100%;
            height: 100%;
            overflow: hidden;
            transition: all 300ms ease-out;
            vertical-align: top;
            box-sizing: border-box;
            white-space: normal;
            
            figure {
                display: flex;
                margin: 0;
            }
            
            div {
                @include aspect-ratio(3, 2);
                width: 100%;
            }
            
            img {
                width: 800px;
                aspect-ratio: auto 800 / 450;
                height: 450px;
                display: block;
                flex: 1 1 auto;
                object-fit: cover;
            }
            
            figcaption {
                align-self: flex-end;
                padding: 20px 20px 0 20px;
                flex: 0 0 auto;
                width: 25%;
                min-width: 150px;
            }
            
            .credit {
                margin-top: 1rem;
                color: rgba(0, 0, 0, 0.5);
                display: block;        
            }
            
            &.scrollable {
                overflow-y: scroll;
            }
        }
        
        .carousel__thumbnails {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            
            margin: 0 -10px;
            
            .carousel__slides + & {
                margin-top: 20px;
            }
            
            li {        
                flex: 1 1 auto;
                max-width: calc((100% / 6) - 20px);  
                margin: 0 10px;
                transition: all 300ms ease-in-out;
            }
            
            label {
                display: block;
                @include aspect-ratio(1,1);
                
                          
                &:hover,
                &:focus {
                    cursor: pointer;
                    
                    img {
                        box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.25);
                        transition: all 300ms ease-in-out;
                    }
                }
            }
            
            img {
                display: block;
                width: 100%;
                height: 150px;
                object-fit: cover;
            }
        }

        .notification-container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 40px 0;
        }

        .notification-container h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .notification-container .buttona,
        .notification-container .buttonb {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            color: white;
            background-color: #003366;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .notification-container .buttona:hover,
        .notification-container .buttonb:hover {
            background-color: #00509E;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="./images/ptu-logo.png" alt="University Logo">
    </div>
    <div class="university-name">
        <span>P</span>uducherry <span>T</span>echnological <span>U</span>niversity
    </div>
</header>
<section>
    <div class="container">
        <div class="carousel">
            <input type="radio" name="slides" checked="checked" id="slide-1">
            <input type="radio" name="slides" id="slide-2">
            <input type="radio" name="slides" id="slide-3">
            <input type="radio" name="slides" id="slide-4">
            <input type="radio" name="slides" id="slide-5">
            <input type="radio" name="slides" id="slide-6">
            <ul class="carousel__slides">
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/audi.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Auditorium of PTU
                        </figcaption>
                    </figure>
                </li>
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/it.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Entrance of Information Technology , Electrical and Instrumentation Engineering
                        </figcaption>
                    </figure>
                </li>
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/ptu-main.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Administrative Block of PTU
                        </figcaption>
                    </figure>
                </li>
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/chem.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Chemical Engineering Department Block
                        </figcaption>
                    </figure>
                </li>
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/eee.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Electrical and Electronic Engineering Department Block
                        </figcaption>
                    </figure>
                </li>
                <li class="carousel__slide">
                    <figure>
                        <div>
                            <img src="./images/image2.jpg" alt="">
                        </div>
                        <figcaption>
                            Photo: Flag Hosting in PTU
                        </figcaption>
                    </figure>
                </li>
            </ul>    
            <ul class="carousel__thumbnails">
                <li>
                    <label for="slide-1"><img src="./images/audi.jpg" alt=""></label>
                </li>
                <li>
                    <label for="slide-2"><img src="./images/it.jpg" alt=""></label>
                </li>
                <li>
                    <label for="slide-3"><img src="./images/ptu-main.jpg" alt=""></label>
                </li>
                <li>
                    <label for="slide-4"><img src="./images/chem.jpg" alt=""></label>
                </li>
                <li>
                    <label for="slide-5"><img src="./images/eee.jpg" alt=""></label>
                </li>
                <li>
                    <label for="slide-6"><img src="./images/image2.jpg" alt=""></label>
                </li>
            </ul>
        </div>
        <div class="notification-container">
    <h1>DYNAMIC ACADEMIC NOTIFICATION SYSTEM</h1>
    <a class="buttona" href="./student/login_student.php">Login as Student</a>
    <a class="buttonb" href="./staff/login_staff.php">Login as Teacher</a>
</div>

    </div>
</section>
</body>
</html>
