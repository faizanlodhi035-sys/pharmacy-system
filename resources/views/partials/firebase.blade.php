<!-- Firebase Compat SDK (Fast, Synchronous, Zero-deferral) -->
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>

<script>
  (function() {
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

    if (!firebase.apps.length) {
      firebase.initializeApp(firebaseConfig);
    }

    const auth = firebase.auth();
    const provider = new firebase.auth.GoogleAuthProvider();
    provider.setCustomParameters({ prompt: 'select_account' });
    const db = firebase.database();

    window.firebaseAuth = auth;
    window.firebaseDb = db;

    async function handleServerAuth(user) {
      const btn = document.getElementById('firebase-google-btn');
      if (btn) btn.innerHTML = '<span>Signing in to Dashboard...</span>';

      try {
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
          alert(data.message || 'Firebase login failed on server.');
          if (btn) btn.innerHTML = '<span>Sign in with Firebase (Google)</span>';
        }
      } catch (err) {
        console.error('Server sync error:', err);
        alert('Server communication error during login: ' + err.message);
        if (btn) btn.innerHTML = '<span>Sign in with Firebase (Google)</span>';
      }
    }

    // Check redirect result on load
    auth.getRedirectResult().then((result) => {
      if (result && result.user) {
        handleServerAuth(result.user);
      }
    }).catch((err) => {
      console.warn('Redirect error:', err);
    });

    window.loginWithFirebaseGoogle = async function() {
      const btn = document.getElementById('firebase-google-btn');
      const originalHtml = btn ? btn.innerHTML : '';
      if (btn) btn.innerHTML = '<span class="animate-pulse">Connecting to Google...</span>';

      try {
        const result = await auth.signInWithPopup(provider);
        if (result && result.user) {
          await handleServerAuth(result.user);
        }
      } catch (error) {
        console.error('Google Sign-in Error:', error);
        if (btn) btn.innerHTML = originalHtml;

        if (error.code === 'auth/popup-blocked' || error.code === 'auth/cancelled-popup-request') {
          if (btn) btn.innerHTML = '<span>Redirecting to Google...</span>';
          auth.signInWithRedirect(provider);
        } else if (error.code === 'auth/unauthorized-domain') {
          alert('⚠️ Firebase Authorized Domain Error:\nPlease wait a moment for Firebase to sync the authorized domain.');
        } else if (error.code === 'auth/popup-closed-by-user') {
          // popup closed by user
        } else {
          alert('Google Sign-In Notice: ' + (error.message || error.code));
        }
      }
    };

    @auth
    const currentRole = "{{ auth()->user()->role }}";
    const userEmailHash = "{{ md5(auth()->user()->email) }}";
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

    try {
      db.ref('users/' + userEmailHash + '/role').on('value', (snapshot) => {
        const val = snapshot.val();
        if (val && val.toLowerCase() !== currentRole.toLowerCase()) {
          handleRoleChange(val);
        }
      });
    } catch (e) {
      console.warn('Realtime listener error:', e);
    }
    @endauth
  })();
</script>
