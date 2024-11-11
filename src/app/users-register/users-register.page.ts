import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service'; 
import { ToastController, ModalController } from '@ionic/angular'; 
import { RegistroModalPage } from '../registro-modal/registro-modal.page';
import { DataStorageService } from '../data-storage-service';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-users-register',
  templateUrl: './users-register.page.html',
  styleUrls: ['./users-register.page.scss'],
})
export class UsersRegisterPage implements OnInit {
 // Propiedades para el registro manual
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

filtroUsuarios: string = '';
employees: any[] = [];
filteredEmployees: any[] = [];
busquedaActiva: boolean = false;
filtroRol: string = '';

// Propiedades para agregar usuario por código
registerMode: string = 'register'; // Selector de modo de registro
userCode: string = ''; // Código de usuario
selectedUser: any = null; // Usuario seleccionado al buscar por código
selectedLevelUser: string = ''; // Nivel de usuario seleccionado
levelUsers: any[] = []; // Niveles de usuario
secondaryCompanies: any[] = []; // Empresas secundarias
isModalOpen = false;

constructor(
  private router: Router,
  private http: HttpClient,
  private toastController: ToastController,
  public authService: AuthService,
  private modalController: ModalController,
  private dataStorageService: DataStorageService,
  private navCtrl: NavController
) {
  // Inicializar etiquetas y textos desde el servicio de almacenamiento
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
  this.getEmployees(); // Obtener empleados al iniciar
  this.loadLevelUsers(); // Cargar niveles de usuario al iniciar
  this.loadSecondaryCompanies(); // Cargar empresas secundarias al iniciar
}

// Cargar niveles de usuario disponibles
async loadLevelUsers() {
  this.http.get('https://siinad.mx/php/get-level-users.php').subscribe(
    async (response: any) => {
      if (response) {
        this.levelUsers = response;
      } else {
        console.error('Error al cargar niveles de usuario');
        await this.mostrarToast('Error al cargar niveles de usuario.', 'danger');
      }
    },
    async (error) => {
      console.error('Error en la solicitud GET:', error);
      await this.mostrarToast('Error al cargar niveles de usuario.', 'danger');
    }
  );
}

// Buscar usuario por código ingresado
async onUserCodeChange() {
  if (this.userCode) {
    const data = { userCode: this.userCode };

    this.http.post('https://siinad.mx/php/get-user-by-code.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.selectedUser = response.user;
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
          this.selectedUser = null;
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al cargar usuario.', 'danger');
        this.selectedUser = null;
      }
    );
  } else {
    this.selectedUser = null;
  }
}

// Cargar empresas secundarias disponibles
async loadSecondaryCompanies() {
  this.http.get('https://siinad.mx/php/get-companies.php').subscribe(
    async (response: any) => {
      if (response) {
        this.secondaryCompanies = response;
      } else {
        console.error('Error al cargar empresas secundarias');
        await this.mostrarToast('Error al cargar empresas secundarias.', 'danger');
      }
    },
    async (error) => {
      console.error('Error en la solicitud GET:', error);
      await this.mostrarToast('Error al cargar empresas secundarias.', 'danger');
    }
  );
}

// Asignar empresa secundaria al usuario seleccionado
async assignCompanyToUser() {
  const data = {
    user_id: this.selectedUser.id,
    company_id: this.authService.selectedId,
    principal: 0,
    levelUser_id: this.selectedLevelUser,
    status: 2
  };

  this.http.post('https://siinad.mx/php/assign-company.php', data).subscribe(
    async (response: any) => {
      if (response.success) {
        console.log('Empresa asignada', response);
        await this.mostrarToast('Empresa asignada con éxito.', 'success');
        this.closeModal();
      } else {
        console.error(response.error);
        await this.mostrarToast(response.error, 'danger');
      }
    },
    async (error) => {
      console.error('Error en la solicitud POST:', error);
      await this.mostrarToast('Error al asignar empresa.', 'danger');
    }
  );
}

// Cerrar modal
closeModal() {
  this.isModalOpen = false;
}

// Mostrar mensajes con ToastController
async mostrarToast(message: string, color: string) {
  const toast = await this.toastController.create({
    message: message,
    color: color,
    duration: 2000
  });
  toast.present();
}

// Navegar hacia atrás
goBack() {
  this.navCtrl.back();
}

// Obtener lista de empleados
async getEmployees() {
  const data = {
    companyId: this.authService.selectedId
  };

  this.http.post('https://siinad.mx/php/searchUsers.php', data).subscribe(
    async (response: any) => {
      if (response.success) {
        this.employees = response.employees;
        this.filteredEmployees = this.employees; 
        console.log('Datos de empleados obtenidos:', this.employees);
      } else {
        await this.mostrarToast('Error en la solicitud', 'danger');
      }
    },
    (error) => {
      console.error('Error al realizar la solicitud:', error);
      this.mostrarToast('Error al realizar la solicitud', 'danger');
    }
  );
}

// Eliminar usuario de la empresa
eliminarUsuario(id: number) {
  if (confirm("¿Estás seguro de que quieres eliminar este usuario?")) {
    this.http.delete(`https://siinad.mx/php/deleteUser.php?id=${id}`).subscribe(
      async (response: any) => {
        if (response.success) {
          await this.mostrarToast('Usuario eliminado exitosamente', 'success');
        } else {
          await this.mostrarToast('Error al eliminar usuario: ' + response.message, 'danger');
        }
        this.getEmployees();
      },
      async (error) => {
        console.error('Error en la solicitud DELETE:', error);
        await this.mostrarToast('Error al eliminar usuario.', 'danger');
        this.getEmployees();
      }
    );
  }
}

// Método para buscar usuarios
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

// Validar si los campos del formulario están completos
async camposCompletos(): Promise<boolean> {
  const regexPuntoCom = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  return (
    typeof this.usuario.nombreUsuario === 'string' && this.usuario.nombreUsuario.trim() !== '' &&
    typeof this.usuario.nombreCompleto === 'string' && this.usuario.nombreCompleto.trim() !== '' &&
    typeof this.usuario.correo === 'string' && this.usuario.correo.trim() !== '' &&
    typeof this.usuario.contrasena === 'string' && this.usuario.contrasena.trim() !== '' &&
    typeof this.usuario.levelUser === 'string' && this.usuario.levelUser.trim() !== '' &&
    this.usuario.correo.includes('@') &&
    regexPuntoCom.test(this.usuario.correo) &&
    this.usuario.contrasena === this.usuario.confirmarContrasena 
  );
}

isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Registrar usuario nuevo
async registrarUsuario() {
  if (await this.camposCompletos()) {
    const data = {
      association_user_id: this.authService.userId,
      nombreUsuario: this.usuario.nombreUsuario,
      nombreCompleto: this.usuario.nombreCompleto,
      correo: this.usuario.correo,
      contrasena: this.usuario.contrasena,
      nombreEmpresa: this.authService.selectedCompany,
      rfcEmpresa: this.authService.selectedRFC,
      levelUser: this.usuario.levelUser
    };

    this.http.post('https://siinad.mx/php/registerAdminCompany.php', data).subscribe(
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
            this.getEmployees();
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

// Limpiar campos del formulario
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
}
