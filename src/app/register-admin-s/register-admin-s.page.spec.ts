import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RegisterAdminSPage } from './register-admin-s.page';

describe('RegisterAdminSPage', () => {
  let component: RegisterAdminSPage;
  let fixture: ComponentFixture<RegisterAdminSPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(RegisterAdminSPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
