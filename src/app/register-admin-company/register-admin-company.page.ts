import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service'; // Importa el servicio AuthService
import { ToastController, ModalController } from '@ionic/angular'; // Importa ModalController y ToastController
import { RegistroModalPage } from '../registro-modal/registro-modal.page';
import { DataStorageService } from '../data-storage-service';
import { NavController } from '@ionic/angular';



@Component({
  selector: 'app-register-admin-company',
  templateUrl: './register-admin-company.page.html',
  styleUrls: ['./register-admin-company.page.scss'],
})
export class RegisterAdminCompanyPage implements OnInit {

  usuario = {
    nombreUsuario: '',
    nombreCompleto: '',
    correo: '',
    contrasena: '',
    confirmarContrasena: '',
    levelUser: ''
  };

  labelRegistrarCuentaUsuarioEmpresa: string;
  labelIngreseDatosParaContinuar: string;

  labelAliasUsuario: string;
  labelNombreCompletoUsuario: string;
  labelCorreo: string;
  labelValidacionCorreo: string;
  labelContrasena: string;
  labelConfirmarContrasena: string;
  labelTipoUsuario: string;
  labelAdministrador: string;
  labelSupervisor: string;
  labelAdministrativo: string;
  ButtonRegistrar: string;


  socioComercialSeleccionado: number;

  filtroUsuarios: string = '';
  employees: any[] = []; // Aquí debes tener la lista completa de usuarios
  filteredEmployees: any[] = [];
  busquedaActiva: boolean = false;
  filtroRol: string = ''; // Agregar la propiedad filtroRol

  constructor(
    private router: Router,
    private http: HttpClient,
    private toastController: ToastController, // Inyecta ToastController
    public authService: AuthService, // Asegúrate de inyectar correctamente el servicio AuthService
    private modalController: ModalController, // Inyecta ModalController
    private dataStorageService: DataStorageService,
    private navCtrl: NavController
  ) {
    this.labelRegistrarCuentaUsuarioEmpresa = dataStorageService.labelRegistrarCuentaUsuarioEmpresa;
    this.labelIngreseDatosParaContinuar = dataStorageService.labelIngreseDatosParaContinuar;
    this.labelAliasUsuario = dataStorageService.labelAliasUsuario;
    this.labelNombreCompletoUsuario = dataStorageService.labelNombreCompletoUsuario;
    this.labelCorreo = dataStorageService.labelCorreo;
    this.labelValidacionCorreo = dataStorageService.labelValidacionCorreo;
    this.labelContrasena = dataStorageService.labelContrasena;
    this.labelConfirmarContrasena = dataStorageService.labelConfirmarContrasena;
    this.labelTipoUsuario = dataStorageService.labelTipoUsuario;
    this.labelSupervisor = dataStorageService.labelSupervisor;
    this.labelAdministrador = dataStorageService.labelAdministrador;
    this.labelAdministrativo = dataStorageService.labelAdministrativo;
    this.ButtonRegistrar = dataStorageService.ButtonRegistrar;


  }

  ngOnInit() {
    this.getEmployees(); // Llama a la función para obtener los empleados al iniciar el componente
  }


  async camposCompletos(): Promise<boolean> {
    // Verificar si todos los campos son strings y no están vacíos
    // Expresión regular para verificar que después del punto (.) haya texto
    const regexPuntoCom = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return (
      typeof this.usuario.nombreUsuario === 'string' && this.usuario.nombreUsuario.trim() !== '' &&
      typeof this.usuario.nombreCompleto === 'string' && this.usuario.nombreCompleto.trim() !== '' &&
      typeof this.usuario.correo === 'string' && this.usuario.correo.trim() !== '' &&
      typeof this.usuario.contrasena === 'string' && this.usuario.contrasena.trim() !== '' &&
      typeof this.usuario.levelUser === 'string' && this.usuario.levelUser.trim() !== '' &&
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
    if (await this.camposCompletos()) {
      const data = {
        association_user_id: this.authService.userId,
        nombreUsuario: this.usuario.nombreUsuario,
        nombreCompleto: this.usuario.nombreCompleto,
        correo: this.usuario.correo,
        contrasena: this.usuario.contrasena,
        nombreEmpresa: this.authService.selectedCompany, // Obtén la empresa seleccionada del servicio AuthService
        rfcEmpresa: this.authService.selectedRFC,
        levelUser: this.usuario.levelUser
      };

      // Realizar la solicitud POST al archivo PHP con los datos de registro
      this.http.post('https://siinad.mx/php/registerAdminCompany.php', data).subscribe(
        async (response: any) => {
          if (response.success) {
            // Registro exitoso, redirigir al usuario a la página de inicio de sesión
            await this.mostrarToast(response.message, 'success');
            const modal = await this.modalController.create({
              component: RegistroModalPage,
              componentProps: { continuarRegistro: false }
            });

            await modal.present();

            const { data } = await modal.onDidDismiss();

            if (data.continuarRegistro) {
              this.limpiarCampos();
              this.getEmployees();
            } else {
              this.router.navigate(['/home']);
            }
          } else {
            // Error en el registro, mostrar un mensaje de error al usuario
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

  async mostrarToast(mensaje: string, color: string) {
    const toast = await this.toastController.create({
      message: mensaje,
      duration: 3000,
      color: color
    });
    toast.present();
  }

  onEnterPressed() {
    // Llama a la función de inicio de sesión cuando se presiona Enter
    this.registrarUsuario();
  }

  limpiarCampos() {
    this.usuario = {
      nombreUsuario: '',
      nombreCompleto: '',
      correo: '',
      contrasena: '',
      confirmarContrasena: '',
      levelUser: ''
    };
  }




  async getEmployees() {
    const data = {
      companyId: this.authService.selectedId
    };

    // Realizar la solicitud POST al archivo PHP con los datos de registro
    this.http.post('https://siinad.mx/php/searchUsers.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          // Éxito en la solicitud, asignar los datos de empleados al array this.employees
          this.employees = response.employees;
          this.filteredEmployees = this.employees; // Al principio, muestra todos los usuarios
          console.log('Datos de empleados obtenidos:', this.employees);
        } else {
          // Error en la solicitud, mostrar un mensaje de error al usuario
          await this.mostrarToast('Error en la solicitud', 'danger');
        }
      },
      (error) => {
        // Manejar errores de red u otros errores
        console.error('Error al realizar la solicitud:', error);
        this.mostrarToast('Error al realizar la solicitud', 'danger');
      }
    );
  }


  eliminarUsuario(id: number) {
    if (confirm("¿Estás seguro de que quieres eliminar este usuario?")) {
      // Realizar la solicitud DELETE al archivo PHP para eliminar el usuario
      this.http.delete(`https://siinad.mx/php/deleteUser.php?id=${id}`).subscribe(
        async (response: any) => {
          if (response.success) {
            await this.mostrarToast('Usuario eliminado exitosamente', 'success');
          } else {
            await this.mostrarToast('Error al eliminar usuario: ' + response.message, 'danger');
          }
          // Actualizar la lista de usuarios después de eliminar uno
          this.getEmployees();
        },
        async (error) => {
          console.error('Error en la solicitud DELETE:', error);
          await this.mostrarToast('Usuario eliminado exitosamente', 'success');
          this.getEmployees();
        }
      );
    }
  }


  // Definir una función para asignar un nombre legible al rol del empleado
  getRolLegible(rol: string): string {
    switch (rol) {
      case 'adminS':
        return 'Administrador único de la Página';
      case 'adminE':
        return 'Administrador único de la Empresa';
      case 'adminEE':
        return 'Administrador único de la Empresa';
      case 'adminPE':
        return 'Administrador único de la Empresa';
      case 'admin':
        return 'Administrador de la empresa';
      case 'superV':
        return 'Supervisor de la empresa';
      case 'adminU':
        return 'Administrativo de la empresa';
      default:
        return 'Rol desconocido';
    }
  }


  openModal(id: number) {
    this.socioComercialSeleccionado = id;
    // Luego puedes abrir el modal con el ID del socio comercial seleccionado
  }


  buscarUsuarios() {
    if (this.filtroUsuarios.trim() !== '') {
      this.filteredEmployees = this.employees.filter(employee =>
        (employee.username.toLowerCase().includes(this.filtroUsuarios.toLowerCase()) ||
          employee.name.toLowerCase().includes(this.filtroUsuarios.toLowerCase())) &&
        (this.filtroRol === '' || employee.role === this.filtroRol)
      );
      this.busquedaActiva = true;
    } else if (this.filtroRol !== '') {
      this.filteredEmployees = this.employees.filter(employee =>
        employee.role === this.filtroRol
      );
      this.busquedaActiva = true;
    } else {
      this.filteredEmployees = this.employees;
      this.busquedaActiva = false;
    }
  }

  goBack() {
    this.navCtrl.back();
  }

}


