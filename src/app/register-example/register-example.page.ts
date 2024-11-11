import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { AuthService } from '@auth0/auth0-angular';
import { HttpClient } from '@angular/common/http';
import { DataStorageService } from '../data-storage-service';

@Component({
  selector: 'app-register-example',
  templateUrl: './register-example.page.html',
  styleUrls: ['./register-example.page.scss'],
})
export class RegisterExamplePage implements OnInit {
  
  username: string;
  password: string;
  labelnombreUsuario: string;
  labelcontrasena: string;
  buttonIniciarSesion: string;
  buttonRecuperarContrasena: string;
  labelIngreseDatosParaContinuar: string;

  constructor(
    private router: Router,
    private toastController: ToastController,
    public auth: AuthService, // Usar AuthService de Auth0
    private http: HttpClient,
    private dataStorageService: DataStorageService
  ) { 
    this.labelnombreUsuario = dataStorageService.labelnombreUsuario;
    this.labelcontrasena = dataStorageService.labelContrasena;
    this.buttonIniciarSesion = dataStorageService.buttonIniciarSesion;
    this.buttonRecuperarContrasena = dataStorageService.buttonRecuperarContrasena;
    this.labelIngreseDatosParaContinuar = dataStorageService.labelIngreseDatosParaContinuar;
  }

  ngOnInit() {
    // Verificar si el usuario ya está autenticado con Auth0
    this.auth.isAuthenticated$.subscribe(isAuthenticated => {
      if (isAuthenticated) {
        // Obtener el perfil del usuario de Auth0
        this.auth.user$.subscribe(user => {
          if (user && user.sub) {
            const auth0UserId = user.sub; // Aquí obtienes el ID único del usuario en Auth0
            console.log('Auth0 User ID (sub):', auth0UserId);
            // Llamar al backend para buscar el usuario en la base de datos usando el sub de Auth0
            this.fetchUserFromDatabase(auth0UserId);
          } else {
            console.error('El ID de usuario de Auth0 (sub) es indefinido');
            this.presentToast('No se pudo obtener el ID de usuario', 'danger');
          }
        });
      }
    });
  }
  

  login() {
    // Usar Auth0 para iniciar sesión
    this.auth.loginWithRedirect();
  }

  logout() {
    // Usar el método logout sin la propiedad 'returnTo'
    this.auth.logout({ logoutParams: { client_id: 'WmUOTlu2Z2i2lkGHxZmWSvrarNv5h9hE' } });
  }

  fetchUserFromDatabase(auth0Sub: string) {
    // Enviar el ID del usuario de Auth0 (sub) al backend para obtener los detalles del usuario en la base de datos
    this.http.post('https://siinad.mx/php/loginAuth0.php', { sub: auth0Sub })
      .subscribe((response: any) => {
        if (response.success) {
          const userId = response.userId; // ID del usuario en tu base de datos
          console.log('User ID from database:', userId);
          this.router.navigate(['/home']); // Redirigir al usuario a la página de inicio
        } else {
          this.presentToast('Usuario no encontrado en la base de datos', 'danger');
        }
      }, error => {
        console.error('Error en la solicitud:', error);
        this.presentToast('Error al verificar el usuario en la base de datos', 'danger');
      });
  }


  async presentToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message: message,
      duration: 3000,
      color: color
    });
    toast.present();
  }

  onEnterPressed() {
    // Llama a la función de inicio de sesión de Auth0 cuando se presiona Enter
    this.login();
  }
}
