import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ToastController, ModalController, LoadingController } from '@ionic/angular'; // Importa LoadingController
import { RegistroModalPage } from '../registro-modal/registro-modal.page';
import { DataStorageService } from '../data-storage-service';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-register-admin-s',
  templateUrl: './register-admin-s.page.html',
  styleUrls: ['./register-admin-s.page.scss'],
})
export class RegisterAdminSPage implements OnInit {

  usuario = {
    nombreUsuario: '',
    nombreCompleto: '',
    correo: '',
    numTelefonico: '',
    contrasena: '',
    confirmarContrasena: '',
    rfc: '',
    nombreEmpresa: '',
    fechaInicio: '',
    fechaFin: ''
  };

  showPassword: boolean = false; // Controla la visibilidad de la contraseña
  showConfirmPassword: boolean = false; // Controla la visibilidad de confirmar contraseña

  showMessageFlag: boolean = false;
  tipoRFC: string = 'fisica'; // Valor por defecto: persona física
  rfcLengthError: string = '';

  labelAdminEmpresas: string;
  labelIngreseDatosParaContinuar: string;
  labelTipoRFC: string;
  labelPersonaFisica: string;
  labelPersonaMoral: string;
  labelRFC: string;
  labelComoApareceConstanciaFiscal: string;
  labelAliasUsuario: string;
  labelNombreCompletoUsuario: string;
  labelCorreo: string;
  labelValidacionCorreo: string;
  labelContrasena: string;
  labelConfirmarContrasena: string;
  labelFechaInicioPeriodo: string;
  labelFechaFinPeriodo: string;
  ButtonRegistrar: string;

  constructor(
    private router: Router,
    private http: HttpClient,
    private toastController: ToastController, 
    private modalController: ModalController,
    private dataStorageService: DataStorageService,
    private navCtrl: NavController,
    private loadingController: LoadingController // Inyecta LoadingController
  ) {
    // Asignación de etiquetas
    this.labelAdminEmpresas = dataStorageService.labelAdminEmpresas;
    this.labelIngreseDatosParaContinuar = dataStorageService.labelAdminEmpresas;
    this.labelTipoRFC = dataStorageService.labelTipoRFC;
    this.labelPersonaFisica = dataStorageService.labelPersonaFisica;
    this.labelPersonaMoral = dataStorageService.labelPersonaMoral;
    this.labelRFC = dataStorageService.labelRFC;
    this.labelComoApareceConstanciaFiscal = dataStorageService.labelComoApareceConstanciaFiscal;
    this.labelAliasUsuario = dataStorageService.labelAliasUsuario;
    this.labelNombreCompletoUsuario = dataStorageService.labelNombreCompletoUsuario;
    this.labelCorreo = dataStorageService.labelCorreo;
    this.labelValidacionCorreo = dataStorageService.labelValidacionCorreo;
    this.labelContrasena = dataStorageService.labelContrasena;
    this.labelConfirmarContrasena = dataStorageService.labelConfirmarContrasena;
    this.labelFechaInicioPeriodo = dataStorageService.labelFechaInicioPeriodo;
    this.labelFechaFinPeriodo = dataStorageService.labelFechaFinPeriodo;
    this.ButtonRegistrar = dataStorageService.ButtonRegistrar;
  }

  ngOnInit() {}

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  toggleConfirmPasswordVisibility() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }

  async camposCompletos(): Promise<boolean> {
    // Expresión regular para verificar que después del punto (.) haya texto
    const regexPuntoCom = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Verificar si todos los campos son strings y no están vacíos
    return (
      typeof this.usuario.nombreUsuario === 'string' && this.usuario.nombreUsuario.trim() !== '' &&
      typeof this.usuario.nombreCompleto === 'string' && this.usuario.nombreCompleto.trim() !== '' &&
      typeof this.usuario.numTelefonico === 'string' && this.usuario.numTelefonico.trim() !== '' &&
      typeof this.usuario.correo === 'string' && this.usuario.correo.trim() !== '' &&
      typeof this.usuario.contrasena === 'string' && this.usuario.contrasena.trim() !== '' &&
      typeof this.usuario.nombreEmpresa === 'string' && this.usuario.nombreEmpresa.trim() !== '' &&
      this.usuario.correo.includes('@') && // Verificar si el campo de correo electrónico contiene '@'
      regexPuntoCom.test(this.usuario.correo) && // Verificar si el campo de correo electrónico tiene el formato correcto
      this.usuario.contrasena === this.usuario.confirmarContrasena // Verificar si la confirmación de contraseña coincide con la contraseña
    );
  }

  isValidEmail(email: string): boolean {
    // Expresión regular para validar el formato del correo electrónico
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  async registrarUsuario() {
    const loading = await this.loadingController.create({
      message: 'Registrando usuario...', // Mensaje que se muestra durante la carga
    });
    
    await loading.present(); // Muestra el indicador de carga

    if (await this.camposCompletos()) {
      const data = {
        nombreUsuario: this.usuario.nombreUsuario,
        nombreCompleto: this.usuario.nombreCompleto,
        correo: this.usuario.correo,
        numTelefonico: this.usuario.numTelefonico,
        contrasena: this.usuario.contrasena,
        rfc: this.usuario.rfc,
        nombreEmpresa: this.tipoRFC === 'fisica' ? this.usuario.nombreEmpresa : (this.tipoRFC === 'moral' ? this.usuario.nombreEmpresa : null),
        fechaInicio: this.usuario.fechaInicio,
        fechaFin: this.usuario.fechaFin
      };

      this.http.post('https://siinad.mx/php/registerAdminS.php', data).subscribe(
        async (response: any) => {
          await loading.dismiss(); // Oculta el indicador de carga

          if (response.success) {
            await this.mostrarToast(response.message, 'success');
            const modal = await this.modalController.create({
              component: RegistroModalPage,
              componentProps: { continuarRegistro: false }
            });

            await modal.present();

            const { data } = await modal.onDidDismiss();

            if (data.continuarRegistro) {
              this.limpiarCampos();
            } else {
              this.router.navigate(['/home']);
            }
          } else {
            await this.mostrarToast(response.message, 'danger');
          }
        },
        async (error) => {
          console.error('Error en la solicitud POST:', error);
          await loading.dismiss(); // Oculta el indicador de carga
          await this.mostrarToast('Error en la solicitud de registro.', 'danger');
        }
      );
    } else {
      await loading.dismiss(); // Oculta el indicador de carga
      await this.mostrarToast('Por favor complete todos los campos obligatorios y verifique el correo electrónico y la contraseña.', 'warning');
    }
  }

  buscarEmpresaPorRFC() {
    this.http.post('https://siinad.mx/php/searchCompanies.php', { rfc: this.usuario.rfc })
      .subscribe(async (response: any) => {
        if (response.success) {
          this.usuario.nombreEmpresa = response.nombreEmpresa;
        } else {
          await this.mostrarToast(response.message, 'danger');
        }
      }, error => {
        console.error('Error en la solicitud:', error);
      });
  }

  async mostrarToast(mensaje: string, color: string) {
    const toast = await this.toastController.create({
      message: mensaje,
      duration: 3000,
      color: color
    });
    toast.present();
  }

  onChangeTipoRFC() {
    this.usuario.rfc = '';
    this.usuario.nombreEmpresa = '';
    this.rfcLengthError = '';
  }

  validateRFC(event: any) {
    if (this.tipoRFC === 'fisica') {
      if (this.usuario.rfc.length >= 13) {
        this.rfcLengthError = '';
      } else {
        this.rfcLengthError = 'El RFC para persona física debe tener 13 dígitos.';
      }
    } else if (this.tipoRFC === 'moral') {
      if (this.usuario.rfc.length >= 12) {
        this.rfcLengthError = '';
      } else {
        this.rfcLengthError = 'El RFC para persona moral debe tener 12 dígitos.';
      }
    }

    if (this.usuario.rfc.length > 13 && this.tipoRFC === 'fisica') {
      this.usuario.rfc = this.usuario.rfc.substring(0, 13);
    } else if (this.usuario.rfc.length > 12 && this.tipoRFC === 'moral') {
      this.usuario.rfc = this.usuario.rfc.substring(0, 12);
    }
  }

  onEnterPressed() {
    this.registrarUsuario();
  }

  limpiarCampos() {
    this.usuario = {
      nombreUsuario: '',
      nombreCompleto: '',
      correo: '',
      numTelefonico: '',
      contrasena: '',
      confirmarContrasena: '',
      rfc: '',
      nombreEmpresa: '',
      fechaInicio: '',
      fechaFin: ''
    };
  }

  showMessage() {
    this.showMessageFlag = true;
  }

  hideMessage() {
    this.showMessageFlag = false;
  }

  resetMessage() {
    // Esta función se llama cuando el campo recibe el foco
    // La usaremos para restablecer el estado del mensaje
    this.showMessageFlag = false;
  }

  goBack() {
    this.navCtrl.back();
  }
}
