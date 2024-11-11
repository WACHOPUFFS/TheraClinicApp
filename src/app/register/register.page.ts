import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { ToastController, ModalController } from '@ionic/angular';
import { RegistroModalPage } from '../registro-modal/registro-modal.page';
import { AuthService } from '../auth.service';
import { DataStorageService } from '../data-storage-service';
import { PhoneNumberUtil } from 'google-libphonenumber';
import { AccountService } from '../account.service';  // Asegúrate de importar tu servicio

@Component({
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
})
export class RegisterPage implements OnInit {

  usuario = {
    nombreUsuario: '',
    nombreCompleto: '',
    correo: '',
    numTelefonico: '',
    contrasena: '',
    confirmarContrasena: '',
    rfc: '',
    roleInCompany: '',
    nombreEmpresa: '',
    fechaInicio: '',
    fechaFin: '',
    empresaLigada: ''
  };

  showMessageFlag: boolean = false;
  tipoRFC: string = 'fisica'; // Valor por defecto: persona física
  rfcLengthError: string = '';
  prefijo: string;
  prefijos: { codigo: string, pais: string }[] = [];
  phoneNumberUtil: PhoneNumberUtil;
  telefonoError: string = ''; // Variable para el mensaje de error de teléfono

  labelRegistrarSocioComercialRol: string;
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
  labelTipoUsuario: string;
  labelCliente: string;
  labelProveedor: string;
  labelClienteProveedor: string;
  labelFechaInicioPeriodo: string;
  labelFechaFinPeriodo: string;
  ButtonRegistrar: string;

  constructor(
    private router: Router,
    private http: HttpClient,
    private toastController: ToastController,
    private modalController: ModalController,
    private authService: AuthService,
    private dataStorageService: DataStorageService,
    private accountService: AccountService  // Inyecta tu servicio
  ) {
    this.phoneNumberUtil = PhoneNumberUtil.getInstance();
    this.obtenerPrefijos();
    this.labelRegistrarSocioComercialRol = dataStorageService.labelRegistrarSocioComercialRol;
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
    this.labelTipoUsuario = dataStorageService.labelTipoRol;
    this.labelCliente = dataStorageService.labelCliente;
    this.labelProveedor = dataStorageService.labelProveedor;
    this.labelClienteProveedor = dataStorageService.labelClienteProveedor;
    this.labelFechaInicioPeriodo = dataStorageService.labelFechaInicioPeriodo;
    this.labelFechaFinPeriodo = dataStorageService.labelFechaFinPeriodo;
    this.ButtonRegistrar = dataStorageService.ButtonRegistrar;
  }

  ngOnInit() {}

  obtenerPrefijos() {
    const regionCodes = this.phoneNumberUtil.getSupportedRegions();
    this.prefijos = regionCodes.map(regionCode => {
      const countryCode = '+' + this.phoneNumberUtil.getCountryCodeForRegion(regionCode);
      const countryName = this.getCountryName(regionCode);
      return { codigo: countryCode, pais: countryName };
    });

    // Ordenar los prefijos de menor a mayor
    this.prefijos.sort((a, b) => parseInt(a.codigo) - parseInt(b.codigo));
  }

  getCountryName(regionCode: string): string {
    // Implementa un método para mapear códigos de región a nombres de países
    // Por simplicidad, usaré un método de marcador de posición
    return regionCode; // Marcador de posición, reemplaza con la implementación real
  }

  async camposCompletos(): Promise<boolean> {
    const regexPuntoCom = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return (
      typeof this.usuario.nombreUsuario === 'string' && this.usuario.nombreUsuario.trim() !== '' &&
      typeof this.usuario.nombreCompleto === 'string' && this.usuario.nombreCompleto.trim() !== '' &&
      typeof this.usuario.correo === 'string' && this.usuario.correo.trim() !== '' &&
      typeof this.usuario.numTelefonico === 'string' && this.usuario.numTelefonico.trim() !== '' &&
      typeof this.usuario.contrasena === 'string' && this.usuario.contrasena.trim() !== '' &&
      typeof this.usuario.nombreEmpresa === 'string' && this.usuario.nombreEmpresa.trim() !== '' &&
      typeof this.usuario.roleInCompany === 'string' && this.usuario.roleInCompany.trim() !== '' &&
      this.usuario.correo.includes('@') &&
      regexPuntoCom.test(this.usuario.correo) &&
      this.usuario.contrasena === this.usuario.confirmarContrasena &&
      this.validarTelefono()
    );
  }

  isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  validarTelefono(): boolean {
    const telefonoRegex = /^\d{10}$/;
    if (!telefonoRegex.test(this.usuario.numTelefonico)) {
      this.telefonoError = 'El número de teléfono debe tener exactamente 10 dígitos numéricos.';
      return false;
    } else {
      this.telefonoError = '';
      return true;
    }
  }

  async registrarUsuario() {
    if (await this.camposCompletos()) {
      const data = {
        idUser: this.authService.userId,
        nombreUsuario: this.usuario.nombreUsuario,
        nombreCompleto: this.usuario.nombreCompleto,
        correo: this.usuario.correo,
        numTelefonico: this.usuario.numTelefonico,
        contrasena: this.usuario.contrasena,
        rfc: this.usuario.rfc,
        roleInCompany: this.usuario.roleInCompany,
        nombreEmpresa: this.tipoRFC === 'fisica' ? this.usuario.nombreEmpresa : (this.tipoRFC === 'moral' ? this.usuario.nombreEmpresa : null),
        fechaInicio: this.usuario.fechaInicio,
        fechaFin: this.usuario.fechaFin,
        empresaLigada: this.authService.selectedCompany,
      };

      this.http.post('https://siinad.mx/php/registrar.php', data).subscribe(
        async (response: any) => {
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
          await this.mostrarToast('Error en la solicitud de registro.', 'danger');
        }
      );
    } else {
      await this.mostrarToast('Por favor complete todos los campos obligatorios y verifique el correo electrónico y la contraseña.', 'warning');
    }
  }

  buscarEmpresaPorRFC() {
    this.http.post('https://siinad.mx/php/searchCompanies.php', { rfc: this.usuario.rfc }).subscribe(
      async (response: any) => {
        if (response.success) {
          this.usuario.nombreEmpresa = response.nombreEmpresa;
        } else {
          await this.mostrarToast(response.message, 'danger');
        }
      },
      error => {
        console.error('Error en la solicitud:', error);
      }
    );
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

  showMessage() {
    this.showMessageFlag = true;
  }

  hideMessage() {
    this.showMessageFlag = false;
  }

  resetMessage() {
    this.showMessageFlag = false;
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
      roleInCompany: '',
      nombreEmpresa: '',
      fechaInicio: '',
      fechaFin: '',
      empresaLigada: ''
    };
  }
}
