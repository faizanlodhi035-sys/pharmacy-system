<!-- Firebase JS SDK Integration -->
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
  import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
  import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-database.js";

  const firebaseConfig = {
    apiKey: "{{ config('services.firebase.api_key', 'AIzaSyBs6PO3Hu_8bQdVsqHETVeBuEr_dZ3a-oE') }}",
    authDomain: "{{ config('services.firebase.auth_domain', 'pharmacymanagesystem.firebaseapp.com') }}",
    databaseURL: "{{ config('services.firebase.database_url', 'https://pharmacymanagesystem-default-rtdb.firebaseio.com') }}",
    projectId: "{{ config('services.firebase.project_id', 'pharmacymanagesystem') }}",
    storageBucket: "{{ config('services.firebase.storage_bucket', 'pharmacymanagesystem.firebasestorage.app') }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id', '227901150233') }}",
    appId: "{{ config('services.firebase.app_id', '1:227901150233:web:591a4ea18dc3b4e7d84688') }}",
    measurementId: "{{ config('services.firebase.measurement_id', 'G-4G8B6Y9X28') }}"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();
  const db = getDatabase(app);

  window.firebaseApp = app;
  window.firebaseAuth = auth;
  window.firebaseDb = db;

  window.loginWithFirebaseGoogle = async function() {
    const btn = document.getElementById('firebase-google-btn');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = 'Signing in...';

    try {
      const result = await signInWithPopup(auth, provider);
      const user = result.user;
      
      const response = await fetch('/login/firebase', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          email: user.email,
          name: user.displayName,
          uid: user.uid
        })
      });

      const data = await response.json();
      if (data.success) {
        window.location.href = data.redirect || '/dashboard';
      } else {
        alert(data.message || 'Firebase login failed.');
        if (btn) btn.innerHTML = originalText;
      }
    } catch (error) {
      console.error('Firebase Auth Error:', error);
      alert('Firebase Google Sign-In Error: ' + error.message);
      if (btn) btn.innerHTML = originalText;
    }
  };

  @auth
  // Real-time Role Sync Listener
  const currentRole = "{{ auth()->user()->role }}";
  const userEmailHash = "{{ md5(auth()->user()->email) }}";
  let isRoleInitializing = true;
  let isUpdatingRole = false;

  function showRoleUpdatedToast(newRole) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-5 right-5 z-[9999] bg-slate-900 text-white px-5 py-4 rounded-xl shadow-2xl flex items-center space-x-3 border border-blue-500/50 animate-bounce';
    toast.innerHTML = `
      <div class="bg-blue-600 p-2 rounded-lg">
        <i class="fa-solid fa-user-shield text-white text-lg"></i>
      </div>
      <div>
        <h5 class="font-bold text-sm">Role Updated!</h5>
        <p class="text-xs text-slate-300">Your role has been changed to <span class="font-bold text-blue-400 uppercase">${newRole}</span>. Updating dashboard...</p>
      </div>
    `;
    document.body.appendChild(toast);
  }

  function handleRoleChange(newRole) {
    if (isUpdatingRole) return;
    if (newRole && newRole.toLowerCase() !== currentRole.toLowerCase()) {
      isUpdatingRole = true;
      showRoleUpdatedToast(newRole);
      setTimeout(() => {
        window.location.reload();
      }, 1200);
    }
  }

  // 1. Firebase RTDB Real-time Listener
  try {
    const roleRef = ref(db, 'users/' + userEmailHash + '/role');
    onValue(roleRef, (snapshot) => {
      const val = snapshot.val();
      if (isRoleInitializing) {
        isRoleInitializing = false;
        if (val && val.toLowerCase() !== currentRole.toLowerCase()) {
          handleRoleChange(val);
        }
      } else {
        handleRoleChange(val);
      }
    });
  } catch (err) {
    console.error('Firebase Realtime Role Listener Error:', err);
  }

  // 2. Fallback Poll for DB/Session Synchronization
  setInterval(async () => {
    if (isUpdatingRole) return;
    try {
      const res = await fetch('/auth/user-role-status', {
        headers: { 'Accept': 'application/json' }
      });
      if (res.status === 401) {
        window.location.href = '/login';
        return;
      }
      if (res.ok) {
        const data = await res.json();
        if (data.role && data.role.toLowerCase() !== currentRole.toLowerCase()) {
          handleRoleChange(data.role);
        }
      }
    } catch (e) {
      // Ignore network intermittent errors
    }
  }, 5000);
  @endauth
</script>
