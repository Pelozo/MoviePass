<nav class="navBar">
     <div>
          <img src='<?=FRONT_ROOT?>views/logo/example.png' alt='logo' width=75px>
          <?php 
          if(isset($_SESSION['user'])){
               echo 'Bienvenido, ' . $_SESSION['user']->getEmail();
          }
          ?>
     </div>
     <ul class="navList">

          <li class="navItem">
               <a class="navLink" href="<?=FRONT_ROOT?>">Inicio</a>
          </li>
          <?php if(isset($_SESSION['user'])){?>
          <?php if($_SESSION['user']->getIdRol() == 1){?>
          <li class="dropdown navItem">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"> <span class="nav-label">Admin</span> <span class="caret"></span></a>
               <ul class="dropdown-menu">
                     <li class="navItem">
                         <a class="navLink" href="<?=FRONT_ROOT?>movie/update">Actualizar peliculas</a>                         
                    </li>
                    <li class="navItem">
                         <a class="navLink" href="<?=FRONT_ROOT?>cinema">Administrar cines</a>                         
                    </li>
                    <li class="navItem">
                         <a class="navLink" href="<?=FRONT_ROOT?>show">Administrar funciones</a>                         
                    </li>
               </ul>
          </li>
          <?php } ?>
          <li class="navItem">
               <a class="navLink" href="<?=FRONT_ROOT?>user/profile">Perfil</a>
          </li>
          <li class="navItem">
               <a class="navLink" href="<?=FRONT_ROOT?>user/logout">Cerrar sesión</a>
          </li>
          <?php }else{?>
          <li class="navItem">
               <a class="navLink" href="<?=FRONT_ROOT?>user/login">Log in</a>
          </li>
          <li class="navItem">
               <a class="navLink" href="<?=FRONT_ROOT?>user/signup">Registrar</a>
          </li>
          <?php }?>
          

          
     </ul>
</nav>