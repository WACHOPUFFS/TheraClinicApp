import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from './auth.service';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SharedService {
  // Variable para almacenar los permisos cargados
  permissions: { section: string, subSection: string | null }[] = [];

  constructor(private http: HttpClient, private authService: AuthService) { }

  // Método para cargar los permisos del usuario desde el backend
  loadPermissions(): Observable<any> {
    const userId = this.authService.userId;
    const companyId = this.authService.selectedId;
    const data = { userId: userId, companyId: companyId };

    // Realiza la solicitud HTTP para cargar los permisos
    return this.http.post('https://siinad.mx/php/loadPermissions.php', data);
  }

  // Método para verificar si el usuario tiene un permiso específico
  hasPermission(section: string, subSection: string | null = null): boolean {
    if (subSection) {
      return this.permissions.some(perm => perm.section === section && perm.subSection === subSection);
    } else {
      return this.permissions.some(perm => perm.section === section);
    }
  }

  // Método para establecer el título según el nivel de usuario
  setTitulo(levelUser: string): string {
    switch (levelUser) {
      case 'adminS':
        return 'Configuraciones de la empresa para AdminS';
      case 'adminE':
        return 'Configuraciones de la empresa para AdminE';
      case 'adminEE':
        return 'Configuraciones de la empresa para AdminEE';
      case 'adminPE':
        return 'Configuraciones de la empresa para AdminPE';
      case 'superV':
        return 'Actualizar solicitudes de empleados';
      case 'admin':
      case 'adminU':
        return 'Solicitudes de empleados';
      default:
        return 'Configuraciones de la empresa';
    }
  }
}
