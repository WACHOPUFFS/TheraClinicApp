import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../auth.service'; // Asegúrate de tener el servicio de autenticación
import { NavController, ToastController } from '@ionic/angular';

@Component({
  selector: 'app-user-business-partner-sections',
  templateUrl: './user-business-partner-sections.page.html',
  styleUrls: ['./user-business-partner-sections.page.scss'],
})
export class UserBusinessPartnerSectionsPage implements OnInit {
  selectedUserType: string = 'all';
  selectedUserId: string;
  selectedSection: string;
  selectedSubSections: string[] = [];
  selectedSubSectionsProvider: string[] = []; // Nueva propiedad para proveedor
  selectedSubSectionsClient: string[] = []; // Nueva propiedad para cliente
  users: any[] = [];
  filteredUsers: any[] = [];
  userTypes: any[] = [];
  sections: string[] = [];
  subSections: string[] = [];
  subSectionsProvider: string[] = []; // Nueva propiedad para proveedor
  subSectionsClient: string[] = []; // Nueva propiedad para cliente
  permissions: any[] = [];

  constructor(private toastController: ToastController, private http: HttpClient, public authService: AuthService, private navCtrl: NavController) { }

  ngOnInit() {
    this.loadUsers();
    this.loadUserTypes();
    this.loadSections();
  }

  async loadUsers() {
    const companyId = this.authService.selectedId;
    const data = { companyId: companyId };

    this.http.post('https://siinad.mx/php/searchUsers.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.users = response.employees;
          this.filteredUsers = this.users;
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al cargar empleados.', 'danger');
      }
    );
  }

  async loadUserTypes() {
    this.http.get('https://siinad.mx/php/get-level-users.php').subscribe(
      async (response: any) => {
        this.userTypes = response;
      },
      async (error) => {
        console.error('Error en la solicitud GET:', error);
        await this.mostrarToast('Error al cargar los tipos de usuario.', 'danger');
      }
    );
  }

  async loadSections() {
    const companyId = this.authService.selectedId;
    const data = { companyId: companyId };

    this.http.post('https://siinad.mx/php/loadCompanySections.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          const allSections = ['repse', 'obras', 'asistencias', 'costos', 'ventas', 'configEmpresa'];
          const assignedSections = response.sections.map((section: { NameSection: string }) => section.NameSection);
          this.sections = allSections.filter(section => assignedSections.includes(section));
          // Asegúrate de que 'configEmpresa' siempre esté en la lista de secciones
          if (!this.sections.includes('configEmpresa')) {
            this.sections.push('configEmpresa');
          }
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al cargar secciones.', 'danger');
      }
    );
  }


  async onUserTypeChange(event: any) {
    const userType = event.target.value;

    if (userType === 'all') {
      this.filteredUsers = this.users;
    } else {
      this.filteredUsers = this.users.filter(user => user.role === userType);
    }
  }

  async onUserChange(event: any) {
    this.selectedUserId = event.target.value;
    await this.loadPermissions();
  }

  async onSectionChange(event: any) {
    this.selectedSection = event.target.value;
    this.loadSubSections(this.selectedSection);
  }

  async loadSubSections(section: string) {
    const subSectionsMap: { [key: string]: string[] } = {
      'repse': ['Nominas', 'SUA', 'Informes IMSS', 'Informes INFONAVIT'],
      'obras': ['sub4', 'sub5', 'sub6'],
      'asistencias': ['sub7', 'sub8'],
      'costos': ['sub9', 'sub10'],
      'ventas': ['sub11', 'sub12'],
      'configEmpresa': ['Adicional Empresa', 'Agregar socio comercial', 'Autorizar socio comercial', 'Codigo Empresa', 'Editar Roles de socios comerciales', 'Registrar usuarios Empresa', 'logo Empresa'],
    };

    const subSectionsProviderMap: { [key: string]: string[] } = {
      'repse': ['Nominas Proveedor', 'SUA Proveedor', 'Informes IMSS Proveedor', 'Informes INFONAVIT Proveedor'],
      'obras': ['sub4', 'sub5', 'sub6'],
      'asistencias': ['sub7', 'sub8'],
      'costos': ['sub9', 'sub10'],
      'ventas': ['sub11', 'sub12'],
      'configEmpresa': ['Adicional Empresa', 'Agregar socio comercial', 'Autorizar socio comercial', 'Codigo Empresa', 'Editar Roles de socios comerciales', 'Registrar usuarios Empresa', 'logo Empresa'],
    };

    const subSectionsClientMap: { [key: string]: string[] } = {
      'repse': ['Nominas Cliente', 'SUA Cliente', 'Informes IMSS cliente', 'Informes INFONAVIT cliente'],
      'obras': ['sub10', 'sub11', 'sub20'],
      'asistencias': ['sub7', 'sub8'],
      'costos': ['sub9', 'sub10'],
      'ventas': ['sub11', 'sub12'],
      'configEmpresa': ['Adicional Empresa', 'Agregar socio comercial', 'Autorizar socio comercial', 'Codigo Empresa', 'Editar Roles de socios comerciales', 'Registrar usuarios Empresa', 'logo Empresa'],
    };

    this.subSections = subSectionsMap[section] || [];

    if (this.authService.selectedRole === 'proveedor') {
      this.subSectionsProvider = subSectionsProviderMap[section] || [];
    } else if (this.authService.selectedRole === 'cliente') {
      this.subSectionsClient = subSectionsClientMap[section] || [];
    }
  }

  async loadPermissions() {
    const companyId = this.authService.selectedId;
    const data = { userId: this.selectedUserId, companyId: companyId };

    this.http.post('https://siinad.mx/php/loadPermissions.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.permissions = response.permissions;
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al cargar permisos.', 'danger');
      }
    );
  }

  async addPermission() {
    const companyId = this.authService.selectedId;
    let selectedSubSections: string[] = [];
  
    if (this.authService.selectedRole === 'proveedor') {
      selectedSubSections = this.selectedSubSectionsProvider;
    } else if (this.authService.selectedRole === 'cliente') {
      selectedSubSections = this.selectedSubSectionsClient;
    } else {
      selectedSubSections = this.selectedSubSections;
    }
  
    const data = {
      userId: this.selectedUserId,
      section: this.selectedSection,
      subSections: selectedSubSections,
      companyId: companyId
    };
  
    this.http.post('https://siinad.mx/php/addPermission.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          selectedSubSections.forEach((subSection: string) => {
            this.permissions.push({ section: this.selectedSection, subSection: subSection });
          });
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al añadir permiso.', 'danger');
      }
    );
  }
  


  async removePermission(section: string, subSection: string) {
    const companyId = this.authService.selectedId;
    const data = {
      userId: this.selectedUserId,
      section: section,
      subSection: subSection,
      companyId: companyId
    };

    this.http.post('https://siinad.mx/php/removePermission.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.permissions = this.permissions.filter(p => !(p.section === section && p.subSection === subSection));
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al eliminar permiso.', 'danger');
      }
    );
  }

  // Método para mostrar el toast
  async mostrarToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message: message,
      duration: 2000,
      color: color,
      position: 'top'
    });
    toast.present();
  }

  goBack() {
    this.navCtrl.back();
  }
}