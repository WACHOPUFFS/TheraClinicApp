import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RegisterAdminCompanyPage } from './register-admin-company.page';

describe('RegisterAdminCompanyPage', () => {
  let component: RegisterAdminCompanyPage;
  let fixture: ComponentFixture<RegisterAdminCompanyPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(RegisterAdminCompanyPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
