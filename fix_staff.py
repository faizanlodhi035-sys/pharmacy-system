import re

def fix_app_jsx():
    with open('G:/taleem dithub/TaleemiDunya-Pro/src/App.jsx', 'r', encoding='utf8') as f:
        content = f.read()
    
    if 'import StaffProfile' not in content:
        content = content.replace("import AddStaff from './pages/school-admin/AddStaff';", 
                                  "import AddStaff from './pages/school-admin/AddStaff';\nimport StaffProfile from './pages/school-admin/StaffProfile';")
    
    if '<Route path="staff/:id" element={<StaffProfile />} />' not in content:
        content = content.replace('<Route path="staff/edit/:id" element={<AddStaff />} />',
                                  '<Route path="staff/edit/:id" element={<AddStaff />} />\n        <Route path="staff/:id" element={<StaffProfile />} />')
                                  
    with open('G:/taleem dithub/TaleemiDunya-Pro/src/App.jsx', 'w', encoding='utf8') as f:
        f.write(content)

def fix_staff_manager():
    with open('G:/taleem dithub/TaleemiDunya-Pro/src/pages/school-admin/StaffManager.jsx', 'r', encoding='utf8') as f:
        content = f.read()
    
    # Replace {member.id} with {member.staffId || member.id}
    content = content.replace("ID: {member.id}", "ID: {member.staffId || member.id}")
    
    with open('G:/taleem dithub/TaleemiDunya-Pro/src/pages/school-admin/StaffManager.jsx', 'w', encoding='utf8') as f:
        f.write(content)

if __name__ == '__main__':
    fix_app_jsx()
    fix_staff_manager()
    print("Done")
