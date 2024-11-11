import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../auth.service';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-company-permissions-sections',
  templateUrl: './company-permissions-sections.page.html',
  styleUrls: ['./company-permissions-sections.page.scss'],
})
export class CompanyPermissionsSectionsPage implements OnInit {

  selectedCompanyId: string;
  selectedSections: string[] = [];
  companies: any[] = [];
  sections: string[] = ['Sistema REPSE', 'Control de proyectos', 'Empleados', 'Incidencias', 'Costos', 'Ventas', 'Configuracion de mi empresa', 'Configuracion de socios comerciales', 'Configuracion de usuarios'];
  permissions: any[] = [];

  constructor(private http: HttpClient, private authService: AuthService, private navCtrl: NavController) { }

  ngOnInit() {
    this.loadCompanies();
  }

  async loadCompanies() {
    this.http.get('https://siinad.mx/php/get-companies.php').subscribe(
      async (response: any) => {
        if (response) {
          this.companies = response;
        } else {
          console.error(response.error);
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        console.error('Error en la solicitud GET:', error);
        await this.mostrarToast('Error al cargar empresas.', 'danger');
      }
    );
  }

  async onCompanyChange(event: any) {
    await this.loadPermissions();
  }

  async onSectionChange(event: any) {
    this.selectedSections = event.detail.value;
  }

  async loadPermissions() {
    const data = { companyId: this.selectedCompanyId };

    this.http.post('https://siinad.mx/php/loadCompanyPermissions.php', data).subscribe(
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
    const data = {
      companyId: this.selectedCompanyId,
      sections: this.selectedSections,
    };

    this.http.post('https://siinad.mx/php/addCompanyPermission.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.selectedSections.forEach(section => {
            this.permissions.push({ NameSection: section });
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

  async removePermission(NameSection: string) {
    const data = {
      companyId: this.selectedCompanyId,
      section: NameSection,
    };

    this.http.post('https://siinad.mx/php/removeCompanyPermission.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          this.permissions = this.permissions.filter(p => p.NameSection !== NameSection);
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

  async mostrarToast(message: string, color: string) {
    const toast = document.createElement('ion-toast');
    toast.message = message;
    toast.color = color;
    toast.duration = 2000;
    document.body.appendChild(toast);
    return toast.present();
  }

  
  goBack() {
    this.navCtrl.back();
  }
}
