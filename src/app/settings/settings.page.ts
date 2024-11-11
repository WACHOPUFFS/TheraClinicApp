import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { AuthService } from '../auth.service';
import { HttpClient } from '@angular/common/http';
import { SharedService } from '../shared.service'; // Importa el servicio compartido

@Component({
  selector: 'app-settings',
  templateUrl: './settings.page.html',
  styleUrls: ['./settings.page.scss'],
})
export class SettingsPage implements OnInit {
  showSubsections = false;
  editEmployee: string = '';

  constructor(
    private navCtrl: NavController,
    public authService: AuthService,
    public sharedService: SharedService, // Inyecta el servicio compartido
 
  ) { }

  ngOnInit() {
    this.sharedService.loadPermissions().subscribe(
      (response: any) => {
        if (response.success) {
          // Asigna los permisos cargados al servicio compartido
          this.sharedService.permissions = response.permissions.map((perm: any) => ({ section: perm.section, subSection: perm.subSection }));
          console.log('Permisos cargados:', this.sharedService.permissions); // Depuración
        } else {
          console.error(response.error);
        }
      },
      (error) => {
        console.error('Error en la solicitud POST:', error);
      }
    );
    this.editEmployee = this.sharedService.setTitulo(this.authService.selectedLevelUser);
  


  
  }

  goBack() {
    this.navCtrl.back();
  }

  toggleSubsections() {
    this.showSubsections = !this.showSubsections;
  }

  closeOptions() {
    this.showSubsections = false;
  }

  hasPermission(section: string, subSection: string | null = null): boolean {
    // Llama al método del servicio compartido para verificar el permiso
    return this.sharedService.hasPermission(section, subSection);
  }


  
}