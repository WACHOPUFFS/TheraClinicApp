import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { AuthService as Auth0Service } from '@auth0/auth0-angular'; // Renombrar el AuthService de Auth0
import { AuthService } from '../auth.service'; // Tu servicio de autenticación personalizado
import { DataStorageService } from '../data-storage-service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {

  labelnombreUsuario: string;
  labelcontrasena: string;
  buttonIniciarSesion: string;
  buttonRecuperarContrasena: string;
  labelIngreseDatosParaContinuar: string;

  constructor(
    private router: Router,
    private http: HttpClient,
    private toastController: ToastController,
    private authService: AuthService, // Tu servicio de autenticación
    private auth0Service: Auth0Service, // Servicio de autenticación de Auth0
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
    this.auth0Service.isAuthenticated$.subscribe(isAuthenticated => {
      if (isAuthenticated) {
        // Obtener el perfil del usuario desde Auth0
        this.auth0Service.user$.subscribe(user => {
          if (user && user.sub) {
            const auth0Sub = user.sub; // ID único del usuario en Auth0
            this.fetchUserFromDatabase(auth0Sub);
          } else {
            this.presentToast('No se pudo obtener el ID de usuario', 'danger');
          }
        });
      }
    });
  }

  login() {
    // Iniciar sesión con Auth0
    this.auth0Service.loginWithRedirect();
  }
  fetchUserFromDatabase(auth0Sub: string) {
    const data = new FormData();
    data.append('sub', auth0Sub);

    this.http.post('https://siinad.mx/php/loginAuth0.php', data)
      .subscribe((response: any) => {
        if (response && response.success) {
          const userId = response.userId;
          const avatar = response.avatar;
          const username = response.username;
          const principalCompanies: any[] = response.principalCompanies;

           // Mapear la información detallada de cada empresa, incluyendo el rol y el RFC
           const mappedPrincipalCompanies = principalCompanies.map(company => ({
            id: company.idCompany,
            name: company.nameCompany,
            role: company.roleInCompany,
            rfc: company.rfcIncompany,
            levelUser: company.levelUser
          }));

          // Guardar el token de sesión y las empresas mapeadas en localStorage
          localStorage.setItem('token', response.token);
          localStorage.setItem('mappedPrincipalCompanies', JSON.stringify(mappedPrincipalCompanies));


           // Llamar al método login de AuthService para guardar los detalles del usuario, incluyendo información detallada de empresas
           this.authService.login(username, userId, avatar, mappedPrincipalCompanies);

          // Redirigir al usuario a la página de inicio
          this.router.navigate(['/home']);
        } else {
          this.presentToast(response ? response.message : 'Usuario no encontrado en la base de datos', 'danger');
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
    // Llama a la función de inicio de sesión cuando se presiona Enter
    this.login();
  }
}
