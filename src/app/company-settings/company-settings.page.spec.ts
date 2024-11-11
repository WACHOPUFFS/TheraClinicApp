import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CompanySettingsPage } from './company-settings.page';

describe('CompanySettingsPage', () => {
  let component: CompanySettingsPage;
  let fixture: ComponentFixture<CompanySettingsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(CompanySettingsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
